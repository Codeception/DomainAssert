<?php

namespace Codeception;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class DomainRule extends Constraint
{
    /**
     * @var string rule
     */
    protected $rule;

    /**
     * @var ExpressionLanguage
     */
    protected $language;

    /**
     * @var array
     */
    protected $values = [];

    public function __construct($rule, array $values = array())
    {
        parent::__construct();
        $this->language = new ExpressionLanguage();
        $this->rule = $rule;
        $this->values = $values;
    }

    /**
     * @return ExpressionLanguage
     */
    public function getLanguage(): ExpressionLanguage
    {
        return $this->language;
    }

    private function convertOtherToArray($other)
    {
        if ($other == null) {
            return $this->values;
        }
        if (!is_array($other)) {
            return $this->values['expected'] = $other;
        }
        return array_merge($other, $this->values);
    }

    protected function matches($other)
    {
        return $this->language->evaluate($this->rule, $this->convertOtherToArray($other));
    }

    protected function failureDescription($other)
    {
        return '`' . $this->rule . '`';
    }

    protected function additionalFailureDescription($other)
    {
        $values = $this->convertOtherToArray($other);
        if (empty($values)) {
            return '';
        }

        $varString = '';
        foreach ($values as $key => $value) {
            $varString .= "[$key]: " . $this->exporter->export($value) . "\n";
        }
        return $varString;
    }


    public function toString()
    {
        return "Expression " . $this->rule;
    }
}