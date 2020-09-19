<?php

namespace CincoDeMauro\LaravelFilebase;

use Filebase\Query;
use Illuminate\Database\Connection as BaseConnection;

class Connection extends BaseConnection
{
    /**
     * The database handler.
     * @var \Database
     */
    protected $db;

    /**
     * The connection handler.
     * @var \Client
     */
    protected $connection;

    /**
     * Create a new database connection instance.
     * @param array $config
     * @throws \Filebase\Filesystem\FilesystemException
     */
    public function __construct(array $config)
    {
        $this->db = $this->createConnection($config);
    }

    public function selectFiles($columns, $wheres = [])
    {
        $query = $this->db;

        if($columns[0] != '*'){
            $query = $query->select($columns);
        }

        foreach ($wheres as $where){
            $query = $query->where($where['column'], strtoupper($where['operator']), $where['value']);
        }

        var_dump($this->db);

        return ($query instanceof Query) ? $query->results() : $query->findAll();
    }

    public function insertFiles($inserts)
    {
        foreach($inserts as $insert){
            $file = $this->db->get($insert['id']);

            foreach ($insert as $field => $value) {
                $file->$field = $value;
            }

            $file->save();
        }
    }

    public function updateFiles($wheres, $values)
    {
        $records = $this->selectFiles(['id'], $wheres);

        foreach($records as $record){
            $file = $this->db->get($record['id']);

            foreach ($values as $field => $value) {
                $file->$field = $value;
            }

            $file->save();
        }
    }

    /**
     * Begin a fluent query against a database collection.
     * @param string $collection
     * @return Query\Builder
     */
    public function collection($collection)
    {
        $query = new \CincoDeMauro\LaravelFilebase\Query\Builder($this, $this->getDefaultQueryGrammar(), $this->getDefaultPostProcessor());

        return $query->from($collection);
    }

    /**
     * Begin a fluent query against a database collection.
     * @param string $table
     * @param string|null $as
     * @return Query\Builder
     */
    public function table($table, $as = null)
    {
        $this->db->table($table);

        return $this->collection($table);
    }

    /**
     * Create a new connection.
     * @param array $config
     * @return Firebase\Client
     * @throws \Filebase\Filesystem\FilesystemException
     */
    protected function createConnection(array $config)
    {
        $default = [
            'dir'            => 'path/to/database/dir',
            'backupLocation' => 'path/to/database/backup/dir',
            'format'         => \Filebase\Format\Json::class,
            'cache'          => true,
            'cache_expires'  => 1800,
            'pretty'         => true,
            'safe_filename'  => true,
            'read_only'      => false
        ];

        $config = array_merge($default, $config);

        return new \CincoDeMauro\LaravelFilebase\Firebase\Client($config);
    }
}
