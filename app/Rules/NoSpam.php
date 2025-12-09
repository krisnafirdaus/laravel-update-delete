<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSpam implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected array $spamWords = ['lottery', 'casion', 'click here', 'viagra']; // lottery != Lottery -> case sensitive

    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $lowerValue = strtolower($value); // Viagra

        foreach ($this->spamWords as $spam){
            if(str_contains($lowerValue, $spam)){ // viagra == viagra -> true
                $fail("The $attribute contains prohibited content.");
                return;
            }
        }
        
    }
}
