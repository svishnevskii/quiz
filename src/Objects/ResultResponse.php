<?php

namespace Components\Tests\Objects;

class ResultResponse
{
    private $isDone = false;
    private $points;
    private $text;
    private $rightAnswerId;

    public function setIsDone(bool $isDone): void
    {
        $this->isDone = $isDone;
    }

    public function getIsDone(): bool
    {
        return $this->isDone;
    }

    public function setPoints(string $points): void
    {
        $this->points = $points;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setRightAnswerId(int $id): void
    {
        $this->rightAnswerId = $id;
    }

    public function getRightAnswerId(): int
    {
        return $this->rightAnswerId;
    }
}
