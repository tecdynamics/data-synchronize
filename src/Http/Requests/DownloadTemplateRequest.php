<?php

namespace Tec\DataSynchronize\Http\Requests;

use Tec\Support\Http\Requests\Request;

class DownloadTemplateRequest extends Request
{
    public function rules(): array
    {
        return [
            'format' => ['required', 'string', 'in:csv,xlsx'],
        ];
    }
}
