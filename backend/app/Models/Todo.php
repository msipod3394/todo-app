<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    // どのテーブルと連携するか
    protected $table = 'todos';

    // 一括代入可能な項目
    protected $fillable = ['title', 'deadline_date', 'completed_at', 'user_id'];

    // データ型キャスト設定
    protected $casts = [
        'deadline_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Userとのリレーション
    public function user()
    {
        // 多対1: Todoは1つのUserに属する
        return $this->belongsTo(User::class);
    }
}
