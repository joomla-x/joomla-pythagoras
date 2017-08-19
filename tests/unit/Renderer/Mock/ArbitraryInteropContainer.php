<?php

namespace Joomla\Tests\Unit\Renderer\Mock;

class ArbitraryInteropContainer implements \Psr\Container\ContainerInterface
{
    private $data = [
        'aic_foo' => 'aic_foo_content',
    ];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for this identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ArbitraryNotFoundException();
        }

        return $this->data[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->data[$id]);
    }
}

class ArbitraryNotFoundException extends \RuntimeException implements \Psr\Container\NotFoundExceptionInterface
{

}
