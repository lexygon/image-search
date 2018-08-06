<?php

namespace App\Service\SearchEngine;


interface Searchable
{
    /**
     * @param string $query
     * @return array
     */
    public function search(string $query) : array;

    /**
     * @param array $results
     * @return array
     */
    public function prepare(array $results) : array;
}