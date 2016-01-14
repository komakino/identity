<?php

namespace Komakino\Identity\Traits;

trait BirthdayTrait {
    private function composeBirthday(){
        extract($this->properties);
        return date_create("{$century}{$year}-{$month}-{$day}");
    }
}