<?php

namespace App\Contracts;

interface ArticelIterface
{
    public function filterByKeyword(string $query): self;
    public function filterBySource(string|array|null $source): self;
    public function filterByCategory(string|array|null $category): self;
    public function filterByAuthor(string|array|null $author): self;
    public function setPage(int $page): self;

    public function search(): array;
    public function get(): array;

    public function getSources(): array;
    public function getCategories(): array;
    public function getAuthors(): array;
}
