<?php

namespace Devhammed\SimpleOrm;

use PDO;
use Exception;
use Throwable;
use ReflectionClass;
use ReflectionAttribute;

abstract class Model
{
    protected string $table;

    protected ReflectionClass $reflection;

    protected static array $connections = [];

    protected static array $cachedConnections = [];

    protected static string $connection = 'default';

    public static function addConnection(string $name, array $config): void
    {
        self::$connections[$name] = $config;
    }

    public function getPDO(): PDO
    {
        if (isset(self::$cachedConnections[static::$connection])) {
            return self::$cachedConnections[static::$connection];
        }

        if (!isset(self::$connections[static::$connection])) {
            throw new Exception('Connection [ ' . static::$connection . ' ] not found.');
        }

        $config = self::$connections[static::$connection];

        if (!is_array($config)) {
            throw new Exception('Connection [ ' . static::$connection . ' ] is not valid.');
        }

        if (!isset($config['dsn'])) {
            throw new Exception('Connection [ ' . static::$connection . ' ] dsn not found.');
        }

        $pdo = new PDO(
            $config['dsn'],
            $config['username'] ?? null,
            $config['password'] ?? null
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$cachedConnections[static::$connection] = $pdo;

        return $pdo;
    }

    public static function findOne(
        string|int $id,
        array $columns = ['*'],
    ): ?static {
        try {
            $instance = static::getInstance();

            $pdo = $instance->getPDO();

            $statement = $pdo->prepare(sprintf(
                'SELECT %s FROM %s WHERE id = :id LIMIT 1;',
                implode(', ', $columns),
                $instance->getTable(),
            ));

            $statement->execute(['id' => $id]);

            $statement->setFetchMode(PDO::FETCH_ASSOC);

            $result = $statement->fetch();

            if (!$result) {
                return null;
            }

            return $instance->fill($result);
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
            return null;
        }
    }

    public static function findAll($columns = ['*']): array
    {
        try {
            $instance = static::getInstance();

            $pdo = $instance->getPDO();

            $statement = $pdo->prepare(sprintf(
                'SELECT %s FROM %s;',
                implode(', ', $columns),
                $instance->getTable()
            ));

            $statement->execute();

            $statement->setFetchMode(PDO::FETCH_ASSOC);

            $result = $statement->fetchAll();

            if (!$result) {
                return [];
            }

            $models = [];

            foreach ($result as $item) {
                $models[] = static::getInstance()->fill($item);
            }

            return $models;
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
            return [];
        }
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $classRef = $this->getReflection();

            if (!$classRef->hasProperty($key)) {
                continue;
            }

            $propRef = $classRef->getProperty($key);

            $columns = $propRef->getAttributes(
                Columns\Column::class,
                ReflectionAttribute::IS_INSTANCEOF,
            );

            if (count($columns) === 0) {
                continue;
            }

            foreach ($columns as $column) {
                $value = $column->newInstance()->unserialize($value);
            }

            $this->{$key} = $value;
        }

        return $this;
    }

    public function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $class = explode('\\', get_class($this));

        $class = end($class);

        $class = preg_replace('/\s+/u', '', ucwords($class));

        $class = preg_replace('/(.)(?=[A-Z])/u', '$1_', $class);

        return strtolower($class) . 's';
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflection ??= new ReflectionClass($this);
    }

    public static function getInstance(...$args): static
    {
        if (!empty($args)) {
            return new static(...$args);
        }

        /** @var static $model */
        $model = (new ReflectionClass(static::class))
            ->newInstanceWithoutConstructor();

        return $model;
    }
}
