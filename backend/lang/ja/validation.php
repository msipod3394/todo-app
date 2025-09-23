<?php

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行はバリデータクラスで使用されるデフォルトのエラーメッセージです。
    | 一部のルールにはサイズルールのように複数のバージョンがあります。
    | ここで自由に各メッセージを調整してください。
    |
    */

    'accepted' => ':attribute は承認されている必要があります。',
    'accepted_if' => ':other が :value の場合、:attribute は承認されている必要があります。',
    'active_url' => ':attribute は有効なURLである必要があります。',
    'after' => ':attribute は :date 以降の日付である必要があります。',
    'after_or_equal' => ':attribute は :date 以降または同日の日付である必要があります。',
    'alpha' => ':attribute は文字のみを含めることができます。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを含めることができます。',
    'alpha_num' => ':attribute は文字と数字のみを含めることができます。',
    'any_of' => ':attribute は無効です。',
    'array' => ':attribute は配列である必要があります。',
    'ascii' => ':attribute はシングルバイトの英数字と記号のみを含めることができます。',
    'before' => ':attribute は :date より前の日付である必要があります。',
    'before_or_equal' => ':attribute は :date 以前または同日の日付である必要があります。',
    'between' => [
        'array' => ':attribute は :min ～ :max 個の要素を持つ必要があります。',
        'file' => ':attribute は :min ～ :max KBである必要があります。',
        'numeric' => ':attribute は :min ～ :max の間である必要があります。',
        'string' => ':attribute は :min ～ :max 文字である必要があります。',
    ],
    'boolean' => ':attribute は true または false である必要があります。',
    'can' => ':attribute には許可されていない値が含まれています。',
    'confirmed' => ':attribute の確認が一致しません。',
    'contains' => ':attribute に必要な値が不足しています。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付である必要があります。',
    'date_equals' => ':attribute は :date と同じ日付である必要があります。',
    'date_format' => ':attribute は :format 形式と一致する必要があります。',
    'decimal' => ':attribute は :decimal 桁の小数である必要があります。',
    'declined' => ':attribute は拒否される必要があります。',
    'declined_if' => ':other が :value の場合、:attribute は拒否される必要があります。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁である必要があります。',
    'digits_between' => ':attribute は :min ～ :max 桁である必要があります。',
    'dimensions' => ':attribute の画像サイズが無効です。',
    'distinct' => ':attribute に重複した値があります。',
    'doesnt_end_with' => ':attribute は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attribute は次のいずれかで始まってはいけません: :values。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute は無効です。',
    'exists' => '選択された :attribute は無効です。',
    'extensions' => ':attribute は次のいずれかの拡張子である必要があります: :values。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute は値を持つ必要があります。',
    'gt' => [
        'array' => ':attribute は :value 個より多い要素を持つ必要があります。',
        'file' => ':attribute は :value KBより大きい必要があります。',
        'numeric' => ':attribute は :value より大きい必要があります。',
        'string' => ':attribute は :value 文字より大きい必要があります。',
    ],
    'gte' => [
        'array' => ':attribute は :value 個以上の要素を持つ必要があります。',
        'file' => ':attribute は :value KB以上である必要があります。',
        'numeric' => ':attribute は :value 以上である必要があります。',
        'string' => ':attribute は :value 文字以上である必要があります。',
    ],
    'hex_color' => ':attribute は有効な16進数カラーコードである必要があります。',
    'image' => ':attribute は画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute は :other に存在する必要があります。',
    'in_array_keys' => ':attribute は次のキーのいずれかを含む必要があります: :values。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'list' => ':attribute はリストである必要があります。',
    'lowercase' => ':attribute は小文字である必要があります。',
    'lt' => [
        'array' => ':attribute は :value 個未満の要素を持つ必要があります。',
        'file' => ':attribute は :value KB未満である必要があります。',
        'numeric' => ':attribute は :value 未満である必要があります。',
        'string' => ':attribute は :value 文字未満である必要があります。',
    ],
    'lte' => [
        'array' => ':attribute は :value 個を超えてはいけません。',
        'file' => ':attribute は :value KB以下である必要があります。',
        'numeric' => ':attribute は :value 以下である必要があります。',
        'string' => ':attribute は :value 文字以下である必要があります。',
    ],
    'mac_address' => ':attribute は有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attribute は :max 個を超えてはいけません。',
        'file' => ':attribute は :max KBを超えてはいけません。',
        'numeric' => ':attribute は :max を超えてはいけません。',
        'string' => ':attribute は :max 文字を超えてはいけません。',
    ],
    'max_digits' => ':attribute は :max 桁を超えてはいけません。',
    'mimes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'mimetypes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'min' => [
        'array' => ':attribute は少なくとも :min 個の要素を持つ必要があります。',
        'file' => ':attribute は少なくとも :min KBである必要があります。',
        'numeric' => ':attribute は少なくとも :min である必要があります。',
        'string' => ':attribute は少なくとも :min 文字である必要があります。',
    ],
    'min_digits' => ':attribute は少なくとも :min 桁である必要があります。',
    'missing' => ':attribute が存在してはいけません。',
    'missing_if' => ':other が :value の場合、:attribute が存在してはいけません。',
    'missing_unless' => ':other が :value でない限り、:attribute が存在してはいけません。',
    'missing_with' => ':values が存在する場合、:attribute が存在してはいけません。',
    'missing_with_all' => ':values がすべて存在する場合、:attribute が存在してはいけません。',
    'multiple_of' => ':attribute は :value の倍数である必要があります。',
    'not_in' => '選択された :attribute は無効です。',
    'not_regex' => ':attribute の形式が無効です。',
    'numeric' => ':attribute は数値である必要があります。',
    'password' => [
        'letters' => ':attribute には少なくとも1文字を含める必要があります。',
        'mixed' => ':attribute には少なくとも1つの大文字と1つの小文字を含める必要があります。',
        'numbers' => ':attribute には少なくとも1つの数字を含める必要があります。',
        'symbols' => ':attribute には少なくとも1つの記号を含める必要があります。',
        'uncompromised' => '指定された :attribute は情報漏洩で使用されています。別の :attribute を選んでください。',
    ],
    'present' => ':attribute が存在している必要があります。',
    'present_if' => ':other が :value の場合、:attribute が存在している必要があります。',
    'present_unless' => ':other が :value でない限り、:attribute が存在している必要があります。',
    'present_with' => ':values が存在する場合、:attribute が存在している必要があります。',
    'present_with_all' => ':values がすべて存在する場合、:attribute が存在している必要があります。',
    'prohibited' => ':attribute は禁止されています。',
    'prohibited_if' => ':other が :value の場合、:attribute は禁止されています。',
    'prohibited_if_accepted' => ':other が承認されている場合、:attribute は禁止されています。',
    'prohibited_if_declined' => ':other が拒否されている場合、:attribute は禁止されています。',
    'prohibited_unless' => ':other が :values に含まれない限り、:attribute は禁止されています。',
    'prohibits' => ':attribute は :other の存在を禁止します。',
    'regex' => ':attribute の形式が無効です。',
    'required' => ':attribute は必須です。',
    'required_array_keys' => ':attribute には次の項目が必要です: :values。',
    'required_if' => ':other が :value の場合、:attribute は必須です。',
    'required_if_accepted' => ':other が承認されている場合、:attribute は必須です。',
    'required_if_declined' => ':other が拒否されている場合、:attribute は必須です。',
    'required_unless' => ':other が :values に含まれない限り、:attribute は必須です。',
    'required_with' => ':values が存在する場合、:attribute は必須です。',
    'required_with_all' => ':values がすべて存在する場合、:attribute は必須です。',
    'required_without' => ':values が存在しない場合、:attribute は必須です。',
    'required_without_all' => ':values がすべて存在しない場合、:attribute は必須です。',
    'same' => ':attribute と :other は一致する必要があります。',
    'size' => [
        'array' => ':attribute は :size 個の要素を含む必要があります。',
        'file' => ':attribute は :size KBである必要があります。',
        'numeric' => ':attribute は :size である必要があります。',
        'string' => ':attribute は :size 文字である必要があります。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります: :values。',
    'string' => ':attribute は文字列である必要があります。',
    'timezone' => ':attribute は有効なタイムゾーンである必要があります。',
    'unique' => ':attribute は既に使用されています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'uppercase' => ':attribute は大文字である必要があります。',
    'url' => ':attribute は有効なURLである必要があります。',
    'ulid' => ':attribute は有効なULIDである必要があります。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 属性ごとのカスタムメッセージをここに指定できます。
    | "attribute.rule" の形式で行を指定してください。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の行は属性プレースホルダをよりわかりやすい名前に置き換えます。
    | 例えば "email" を "メールアドレス" に置き換えるなど。
    |
    */

    'attributes' => [
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'name' => '名前',
        "title" => "タイトル",
        "deadline_date" => "期限",
    ],

];
