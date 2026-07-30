# Ленивость графа зависимостей: план рефакторинга

Черновик от 2026-07-30. Контекст: на хите, где плагин зарегистрировал фильтр и тот не сработал,
строится ~30-40 объектов (весь HTTP-стек, middleware/валидационный слой, `Handler` с `View`,
`Database`, `Logger`), читается `php://input` и генерируется UUID. Целевое состояние — десяток
дешёвых объектов, ноль I/O, ноль обращений к БД.

## Диагноз

Точка отсечения выбрана верно: `Hook::__invoke()` только регистрирует замыкание в WP, а
`Pipeline`/`Request` создаются внутри `callback()`. Обесценивает её то, что **весь тяжёлый граф
приезжает конструкторами по пути к этой точке**, и то, что билдер трогает фабрику на этапе сборки
(`HooksBuilder::filter()` → `withMiddlewares()`).

Пять проблем, в порядке вклада:

1. `HooksBuilder`/`RoutesBuilder` инжектят `PipelineFactoryInterface`, `MiddlewaresFactoryInterface`,
   `HandlerInterface`, `ResponseFactoryInterface` инстансами → строится всё.
2. `Server` принимает `string $input` → `php://input` читается до конструктора, на каждом хите.
3. `RequestFactory`/`ResponseFactory` принимают `UuidInterface` инстансом → `wp_generate_uuid4()`
   на каждом хите (и это же причина того, что класс не автовайрится: `Uuid::__construct` —
   `protected`).
4. `Pipeline` создаётся заново на каждое срабатывание колбэка → `array_reverse`/`array_reduce`
   по 20 раз на `the_content` в архиве.
5. Мелочи: `withMiddlewares(null)` на пустом месте, `withHook()` спредит массив на каждом вызове.

## Целевая архитектура

Три идеи, всё остальное — следствия.

**1. Ленивость выражается одним паттерном — виртуальный прокси.** Класс реализует тот же интерфейс,
держит `Closure`-резолвер, материализует настоящий объект при первом обращении и делегирует.
Включается биндингом в контейнере, снимается без правок кода. Прокси не хранят состояния и ничего
не мирроят, кроме сигнатур.

Почему не иначе:

- `->lazy()` из php-di — генерирует сабкласс через ProxyManager, а весь фреймворк на `readonly class`;
  PHP не даёт не-readonly классу наследовать readonly. Плюс это навязало бы
  `friendsofphp/proxy-manager-lts` пользователям библиотеки.
- Нативные lazy objects PHP 8.4 (`newLazyGhost`/`newLazyProxy`) сабкласс не создают и с `readonly`
  работают — но 8.4 в мире WP пока редкость, минимальная версия остаётся 8.2.
- Провайдер (`provide(): T`) — рабочий вариант, но протекает в сигнатуры билдеров и хуков; прокси
  этого не делает.
- Общий `Lazy` со `mixed` — дыра в типах на 8.2 ради замены одной строки `??=`. Типизированное
  свойство с `??=` и есть встроенный в язык `Lazy`.

**2. Сервисы не конфигурируются в рантайме.** `with*`-цепочки на `PipelineFactory` уходят: middlewares
и политика обработки исключений становятся **аргументами исполнения**. Именно двойная роль
(«DI-сервис и value object одновременно») делала ленивость контейнера бесполезной — прокси
инициализировался на `withMiddlewares()` в момент сборки.

**3. Граница — «регистрация ↔ исполнение», а не «hooks ↔ routes».** Физически она внутри
`Hook::callback()` и `Route::callback()`: только там прокси впервые дёргаются. Правило: всё, что
вычисляется до `add_filter`/`add_action`/`register_rest_route`, — данные; всё тяжелее данных живёт
за швом.

### Четыре шва

| Прокси | Интерфейс | Что не строится, пока не позвали |
|---|---|---|
| `Pipeline\Lazy\PipelineFactory` | 2 метода | `RequestFactory`: `Uuid`, `Url`+`Query`, `Headers`, `Body`+кодеры+нормализаторы+`MediaType`, `Routes`, `Server` (и чтение `php://input`) |
| `Pipeline\Middlewares\Lazy\MiddlewaresFactory` | 2 метода | `MiddlewaresBuilder`, валидаторы, `Database`+`Cache`+`wpdb`, `Logger` — не строятся даже когда хук сработал, если middlewares не объявлены |
| `Http\Server\Response\Lazy\ResponseFactory` | 1 метод | `Uuid`, `Headers`, `Body` на роутной стороне |
| `Exceptions\Handler\Lazy\Handler` | 1 метод | `Negotiator`, `AcceptFactory`, `ResponseFactory`, `ViewFactory`+`Renderer`+`Escaper` — до фактического исключения |

Имена по существующей конвенции вариантных каталогов (`Http\Coders\Json\Coder`,
`Hooker\Hooks\Filter\Hook`, `Database\Cache\Select` — декоратор рядом с базовым классом).

Самый ценный шов — второй: «хук сработал, middlewares не объявлены» — самый частый случай в WP,
и за ним прячется 13 объектов плюс обращения к глобальному `$wpdb` и `wc_get_logger()`.

## Шаги

Порядок такой, что каждый шаг проверяем отдельно.

### Шаг 0. Довести до собираемости

Рефакторинг бессмысленно начинать на неработающем графе. Как минимум:

- `container/definitions/pipeline.php` не содержит `PipelineFactoryInterface` и
  `MiddlewaresFactoryInterface` — добавить.
- `Exceptions\Handler\Handler` импортирует `Http\Negotiation\NegotiatorInterface`, а в дереве
  `Http\Negotiator\` — поправить импорт.
- `Uuid::__construct` — `protected`, а инжектится `UuidInterface`; закрывается шагом 5.

Критерий: плагин активируется, хук регистрируется, фильтр отдаёт контент.

### Шаг 1. Middlewares — аргумент исполнения

```php
interface PipelineFactoryInterface
{
    public function create(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
        ?array $routes = null,
        ?Closure $middlewares = null,
        Policy $policy = Policy::Propagate,
    ): PipelineInterface;

    public function createFromServer(
        ?Closure $middlewares = null,
        Policy $policy = Policy::Propagate,
    ): PipelineInterface;
}
```

- Уходят `middlewares()`, `withMiddlewares()`, `withoutMiddlewares()`, `handler()`, `withHandler()`,
  `withoutHandler()`.
- `PipelineFactory` конструктором берёт `RequestFactoryInterface`, `MiddlewaresFactoryInterface`,
  `HandlerInterface` (уже не nullable) и сам делает `tryCreate($middlewares)`.
- `MiddlewaresFactoryInterface` возвращает массив: `create(Closure): array`,
  `tryCreate(?Closure): array` (пусто вместо `null`). Докблок `@return list<MiddlewareInterface>`.
- `Pipeline` принимает `array $middlewares = []`; проверка `!== null` уходит — `array_reduce` над
  пустым массивом сам вернёт исходное замыкание.
- **`Middlewares` и `MiddlewaresInterface` удаляются.** Класс появился только чтобы поглотить ранний
  вызов рецепта из `HooksBuilder::filter()`; после этого шага рецепт вниз уходит сырым `?Closure`, и
  обёртка теряет основание. Отсрочка не исчезает — её носителем становится позиция вызова
  (`tryCreate()` дёргается уже из `Hook::callback()`), а граф билдера закрывает шов из шага 4.
- Валидация рецепта («closure must return middlewares builder instance») остаётся в
  `MiddlewaresFactory` — одно место.

`MiddlewaresFactory` **не** удаляется: её узкий интерфейс из двух методов и есть поверхность шва.
Убрать её — значит инжектить в `PipelineFactory` либо `MiddlewaresBuilderInterface` (12 методов
проксировать), либо голый `Closure`-резолвер (возврат к тому, от чего ушли, плюс вторая
ответственность у фабрики).

Альтернатива, если не хочется терять настоящий тип цепочки: оставить `Middlewares`, но сменить его
основание с ленивости на поведение — `compose(Closure): Closure` с `array_reverse`/`array_reduce`
внутри, тогда `Pipeline` не знает об устройстве цепочки. Перформанс-аргумента здесь нет (после
шага 3 `compose` выполняется один раз за хит на хук), только распределение ответственности.

### Шаг 2. Политика обработки исключений вместо инжекта `Handler`

```php
enum Policy    // Pipeline\Exceptions\Policy\Policy
{
    case Propagate;
    case Handle;
}
```

`PipelineFactory` переводит политику в `?HandlerInterface` для `Pipeline`: `Handle` → передать
хендлер, `Propagate` → `null`. `Pipeline` остаётся тупым и не меняется.

Зачем: хуки **не должны** получать обработку исключений. Иначе фильтр вернёт в цепочку WP
`ResponseInterface` вместо строки и сломает `the_content`. Роуты передают `Policy::Handle`, хуки —
дефолт `Propagate` (текущее поведение: исключение уходит в WP).

`HandlerInterface` уходит из конструктора `RoutesBuilder`.

### Шаг 3. Билдеры и хуки/роуты держат данные

```php
readonly class HooksBuilder implements HooksBuilderInterface
{
    public function __construct(
        protected PipelineFactoryInterface $pipelineFactory,   // ленивый биндинг
        protected array $hooks = [],
    ) {
    }

    public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewares = null): static
    {
        return $this->withHook(
            new Filter\Hook($this->pipelineFactory, $name, $closure, $priority, $middlewares),
        );
    }
}
```

```php
readonly class Hook implements HookInterface    // Filter
{
    protected PipelineInterface $pipeline;

    public function __construct(
        protected PipelineFactoryInterface $pipelineFactory,
        protected string $name,
        protected Closure $closure,
        protected int $priority = 10,
        protected ?Closure $middlewares = null,
    ) {
    }

    public function __invoke(): void
    {
        add_filter($this->name, $this->callback(...), $this->priority, PHP_INT_MAX);
    }

    protected function callback(...$args): mixed
    {
        $pipeline = $this->pipeline ??= $this->pipelineFactory->createFromServer($this->middlewares);

        return $pipeline(fn($request) => ($this->closure)($request, ...$args));
    }
}
```

- `HookInterface` не меняется — унификация и полиморфизм регистрации сохранены.
- Мемо `$this->pipeline ??=` закрывает проблему 4.
- `protected function pipelineFactory(?Closure)` в билдерах уходит вместе с клонами (проблема 5).
- `RoutesBuilder` остаётся с двумя зависимостями: `PipelineFactoryInterface` и
  `ResponseFactoryInterface` (обе ленивые); `Route` дополнительно передаёт `Policy::Handle`.
- Заодно: `withHook`/`withRoute` можно избавить от спреда на каждый вызов, но это косметика.

### Шаг 4. Четыре прокси

Шаблон один на все четыре:

```php
readonly class PipelineFactory implements PipelineFactoryInterface    // Pipeline\Lazy
{
    protected PipelineFactoryInterface $pipelineFactory;

    public function __construct(
        protected Closure $resolver,
    ) {
    }

    public function createFromServer(?Closure $middlewares = null, Policy $policy = Policy::Propagate): PipelineInterface
    {
        return $this->pipelineFactory()->createFromServer($middlewares, $policy);
    }

    public function create(string $method, string $url, array $headers = [], ?string $body = null, ?array $routes = null, ?Closure $middlewares = null, Policy $policy = Policy::Propagate): PipelineInterface
    {
        return $this->pipelineFactory()->create($method, $url, $headers, $body, $routes, $middlewares, $policy);
    }

    protected function pipelineFactory(): PipelineFactoryInterface
    {
        return $this->pipelineFactory ??= ($this->resolver)();
    }
}
```

Ключевая деталь второго прокси — короткое замыкание до резолва:

```php
public function tryCreate(?Closure $closure): array    // Pipeline\Middlewares\Lazy
{
    if ($closure === null) {
        return [];    // билдер, валидаторы, Database и Logger остаются непостроенными
    }

    return $this->create($closure);
}
```

Биндинги (php-di, пока он):

```php
Pipeline\PipelineFactoryInterface::class => factory(
    fn(ContainerInterface $c) => new Pipeline\Lazy\PipelineFactory(
        fn() => $c->get(Pipeline\PipelineFactory::class),
    ),
),
```

`Pipeline\PipelineFactory::class => autowire()` и т.п. для настоящих классов — рядом.

**Все ленивые биндинги обязаны быть синглтонами.** Мемо живёт на инстансе прокси; при
не-синглтонном скоупе получишь не оптимизацию, а размножение HTTP-стеков.

### Шаг 5. Остатки эагерности внутри `RequestFactory`

Формально они уже за швом (сработают только когда пайплайн пошёл), но чинить стоит:

- `Server` не должен принимать `string $input`. Либо `Closure(): string`, либо чтение `php://input`
  внутри `body()` с мемо. Для не-multipart поток перечитывается, так что безопасно.
- `RequestFactory` и `ResponseFactory` принимают `UuidFactoryInterface` и вызывают `create()` при
  сборке `Request`/`Response`. Это же снимает вопрос с `protected` конструктором `Uuid`.

### Шаг 6. Зафиксировать идиому

Отложенное значение выражается всегда одинаково: типизированное свойство + `??=` внутри
`protected` геттера, публичные методы ходят только через геттер. Сейчас `Middlewares` и
`RequestFactory` делают это инлайном в публичных методах — привести к одной форме и записать в
CONTRIBUTING. Различать при этом две роли, они не взаимозаменяемы:

- **ленивость сборки** — прокси, решение биндинга, снимается без правок кода;
- **мемоизация в рамках хита** (`RequestFactory::$request`, `Hook::$pipeline`) — это кэш, а не лень;
  цель — дедупликация, отсрочка побочна.

## Проверка

Ленивость надо защитить тестами, иначе она отвалится на первом же рефакторинге. По одному тесту
на шов, с падающим резолвером:

```php
$pipelineFactory = new Pipeline\Lazy\PipelineFactory(
    fn() => throw new LogicException('фабрика не должна резолвиться'),
);
// собрать HooksBuilder, зарегистрировать фильтр, прогнать Hooker
// тест зелёный ⇒ на регистрации граф не строится
```

Обязательный набор:

1. регистрация хуков не резолвит `PipelineFactory`;
2. регистрация роутов не резолвит `PipelineFactory` и `ResponseFactory`;
3. **сработавший хук без middlewares не резолвит `MiddlewaresFactory`** — главный тест;
4. пайплайн без исключения не резолвит `Handler`;
5. многократное срабатывание фильтра создаёт `Pipeline` один раз (счётчик в резолвере);
6. `Request` создаётся один раз на хит при нескольких сработавших хуках.

Плюс существующие тесты `Pipeline`/`Middlewares` переписать под массив.

## Риски и грабли

- **Ошибки конфигурации переезжают с boot на исполнение.** Опечатка в middlewares-замыкании в
  `hooks.php` всплывёт внутри цепочки фильтров WP, а не при активации. Это общая плата за лень;
  если мешает — сделать отдельный дешёвый проход валидации деклараций на активации плагина.
- **`readonly` + `??=`** работает только для неинициализированного типизированного свойства
  (`??=` опирается на `isset`). Не инициализировать такие поля в конструкторе.
- **Прокси прячет стоимость вызова**: обычный с виду метод может собрать полстека. Стандартный
  минус паттерна, принимаем.
- **Не давать хукам `Handler`** ни при каких обстоятельствах (см. шаг 2).
- `PHP_INT_MAX` в `accepted_args` к этому рефакторингу отношения не имеет и остаётся как есть.
- Когда PHP 8.4 в WP станет реальностью — все четыре прокси можно удалить, заменив нативными lazy
  objects в биндингах; шаги 1-3 при этом остаются нужными (иначе прокси инициализируется на
  конфигурировании).

## Что осталось решить

1. `Middlewares` — удалить (массив) или оставить с `compose()`. Склоняюсь к удалению: изначально
   так и было, а вернулся класс только под ленивость.
2. `Policy` — enum (как в плане, по стилю остальных перечислений) или `bool $handleExceptions`.
3. `ResponseFactory` — прокси (12 строк, как в плане) или редизайн: нормализацию возврата в
   `ResponseInterface` унести за шов, тогда роутам фабрика ответов не нужна вовсе.
4. Нужен ли проход валидации деклараций на активации (см. риски).

## Баланс

Минус два файла (`Middlewares`, `MiddlewaresInterface`), плюс пять (четыре прокси + `Policy`),
минус шесть методов в `PipelineFactoryInterface`. `Pipeline` и билдеры становятся проще, чем сейчас.

На хите с одним незарегистрировавшимся... точнее — зарегистрированным и не сработавшим фильтром
остаётся: `Application`, контейнер, `HooksBuilder`, хуки, `RouterFactory`, `RoutesBuilder`, роуты и
четыре прокси по одному замыканию. Ни чтения `php://input`, ни UUID, ни `$wpdb`, ни `View`.
