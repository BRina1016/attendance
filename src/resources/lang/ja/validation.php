<?php

return [
    'required' => ':attributeを入力してください。',
    'string' => ':attributeは文字列である必要があります。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'email' => ':attributeは正しい形式のメールアドレスを入力してください。',
    'regex' => ':attributeは正しい形式で入力してください。',
    'unique' => ':attributeはすでに使用されています。',
    'confirmed' => ':attributeが一致しません。',
    'name.regex' => '名前は全角文字で入力してください。',

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
    ],
];
