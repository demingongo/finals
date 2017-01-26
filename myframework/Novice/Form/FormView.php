<?php

namespace Novice\Form;

use Symfony\Component\Form\Exception\BadMethodCallException;


class FormView implements \ArrayAccess, \IteratorAggregate, \Countable
{

    public $fields = array();

    /**
     * Is the form attached to this renderer rendered?
     *
     * Rendering happens when either the widget or the row method was called.
     * Row implicitly includes widget, however certain rendering mechanisms
     * have to skip widget rendering when a row is rendered.
     *
     * @var bool
     */
    private $rendered = false;

    public function __construct()
    {
    }

    /**
     * Returns whether the view was already rendered.
     *
     * @return bool Whether this view's widget is rendered.
     */
    public function isRendered()
    {
        $hasFields = 0 < count($this->fields);

        if (true === $this->rendered || !$hasFields) {
            return $this->rendered;
        }

        if ($hasFields) {
            foreach ($this->fields as $child) {
                if (!$child->isRendered()) {
                    return false;
                }
            }

            return $this->rendered = true;
        }

        return false;
    }

    /**
     * Marks the view as rendered.
     *
     * @return FormView The view object.
     */
    public function setRendered()
    {
        $this->rendered = true;

        return $this;
    }

    /**
     * Returns a child by name (implements \ArrayAccess).
     *
     * @param string $name The child name
     *
     * @return FormView The child view
     */
    public function offsetGet($name)
    {
        return $this->fields[$name];
    }

    /**
     * Returns whether the given child exists (implements \ArrayAccess).
     *
     * @param string $name The child name
     *
     * @return bool Whether the child view exists
     */
    public function offsetExists($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Implements \ArrayAccess.
     *
     * @throws BadMethodCallException always as setting a child by name is not allowed
     */
    public function offsetSet($name, $value)
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * Removes a child (implements \ArrayAccess).
     *
     * @param string $name The child name
     */
    public function offsetUnset($name)
    {
        unset($this->fields[$name]);
    }

    /**
     * Returns an iterator to iterate over fields (implements \IteratorAggregate).
     *
     * @return \ArrayIterator The iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Implements \Countable.
     *
     * @return int The number of fields views
     */
    public function count()
    {
        return count($this->fields);
    }
}
