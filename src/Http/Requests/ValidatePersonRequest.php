<?php

namespace LaravelEnso\People\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LaravelEnso\Helpers\Traits\FiltersRequest;

class ValidatePersonRequest extends FormRequest
{
    use FiltersRequest;

    public function authorize()
    {
        return $this->emailUnchagedForUser();
    }

    public function rules()
    {
        return [
            'title' => 'integer|nullable',
            'name' => 'required|max:50',
            'appellative' => 'string|max:12|nullable',
            'uid' => ['string', 'nullable', $this->unique('uid')],
            'email' => ['email', 'nullable', $this->unique('email')],
            'phone' => 'max:30|nullable',
            'birthday' => 'nullable|date',
            'bank' => 'string|nullable',
            'bank_account' => 'string|nullable',
            'position' => 'integer|nullable',
            'notes' => 'string|nullable',
            'companies' => 'array',
            'companies.*' => 'exists:companies,id',
            'company' => 'nullable|exists:companies,id|in:'.implode(',', $this->get('companies')),
        ];
    }

    protected function unique(string $attribute)
    {
        return Rule::unique('people', $attribute)
            ->ignore(optional($this->route('person'))->id);
    }

    private function emailUnchagedForUser()
    {
        return ! optional($this->route('person'))->hasUser()
            || $this->get('email') === $this->route('person')->email;
    }
}
