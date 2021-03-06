<?php namespace Neomerx\JsonApi\Schema;

/**
 * Copyright 2015-2018 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Closure;
use Neomerx\JsonApi\Contracts\Schema\RelationshipObjectInterface;
use Neomerx\JsonApi\Factories\Exceptions;

/**
 * @package Neomerx\JsonApi
 */
class RelationshipObject implements RelationshipObjectInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var object|array|null
     */
    private $data;

    /**
     * @var array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface>
     */
    private $links;

    /**
     * @var object|array|null|Closure
     */
    private $meta;

    /**
     * @var bool
     */
    private $isShowData;

    /**
     * @var bool
     */
    private $isRoot;

    /**
     * @var bool
     */
    private $isMetaEvaluated = false;

    /**
     * @var bool
     */
    private $isDataEvaluated = false;

    /**
     * @param string|null               $name
     * @param object|array|null|Closure $data
     * @param array<string,\Neomerx\JsonApi\Contracts\Schema\LinkInterface> $links
     * @param object|array|null|Closure $meta
     * @param bool                      $isShowData
     * @param bool                      $isRoot
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        ?string $name,
        $data,
        array $links,
        $meta,
        bool $isShowData,
        bool $isRoot
    ) {
        $isOk = (($isRoot === false && $name !== null) || ($isRoot === true && $name === null));
        $isOk ?: Exceptions::throwInvalidArgument('name', $name);

        $this->setName($name)->setData($data)->setLinks($links)->setMeta($meta);
        $this->isShowData = $isShowData;
        $this->isRoot     = $isRoot;
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->isDataEvaluated === false) {
            $this->isDataEvaluated = true;

            if ($this->data instanceof Closure) {
                /** @var Closure $data */
                $data = $this->data;
                $this->setData($data());
            }
        }

        assert(is_array($this->data) === true || is_object($this->data) === true || $this->data === null);

        return $this->data;
    }

    /**
     * @param object|array|null|Closure $data
     *
     * @return RelationshipObject
     */
    public function setData($data): self
    {
        assert(is_array($data) === true || $data instanceof Closure || is_object($data) === true || $data === null);

        $this->data            = $data;
        $this->isDataEvaluated = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     *
     * @return self
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        if ($this->isMetaEvaluated === false && $this->meta instanceof Closure) {
            $meta       = $this->meta;
            $this->meta = $meta();
        }

        $this->isMetaEvaluated = true;

        return $this->meta;
    }

    /**
     * @param mixed $meta
     *
     * @return self
     */
    public function setMeta($meta): self
    {
        $this->meta            = $meta;
        $this->isMetaEvaluated = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isShowData(): bool
    {
        return $this->isShowData;
    }

    /**
     * @inheritdoc
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }
}
