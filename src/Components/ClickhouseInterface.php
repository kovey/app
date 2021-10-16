<?php
/**
 * @description clickhouse 接口
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-10-15 17:55:13
 *
 */
namespace Kovey\App\Components;

interface ClickhouseInterface
{
    public function insert(string $table, Array $data);

    public function update(string $table, Array $data, Array $where);

    public function fetchRow(string $sql, Array $bindData = array()) : Array;

    public function fetchAll(string $sql, Array $bindData = array()) : Array;

    public function  fetchByPage(string $sql, Array $bindData = array()) : Array;
}
