<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 13:15:12
 *
 */
namespace KoveyException\App\Components;

interface AutoloadInterface
{
    public function addLocalPath(string $path) : AutoloadInterface;
}
