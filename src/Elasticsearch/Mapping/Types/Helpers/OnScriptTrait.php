<?php

declare(strict_types=1);

namespace Elasticsearch\Mapping\Types\Helpers;

use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

trait OnScriptTrait
{
    private OnScriptError $on_script_error = OnScriptError::FAIL; // fail or continue
    private ?string $script;

    public function getOnScriptError(): OnScriptError
    {
        return $this->on_script_error;
    }

    public function setOnScriptError(OnScriptError $on_script_error): void
    {
        if (false === in_array($on_script_error, OnScriptError::cases(), true)) {
            throw new RuntimeException('Value for on_script_error must be fail or continue. Value "' . $on_script_error . '" given.');
        }
        $this->on_script_error = $on_script_error;
    }

    public function getScript(): ?string
    {
        return $this->script;
    }

    public function setScript(?string $script): void
    {
        $this->script = $script;
    }

    protected function provideOnScriptAsArray(ArrayCollection $array): void
    {
        if (OnScriptError::FAIL !== $this->getOnScriptError()) {
            $array->set('on_script_error', $this->getOnScriptError());
        }

        if (null !== $this->getScript()) {
            $array->set('script', $this->getScript());
        }
    }
}
