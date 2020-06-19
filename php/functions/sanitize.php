<?php
/*******************************************
 * Sanitization
 * 
 * 使用者如果不小心輸入引號 ''，會讓程式碼解讀錯誤
 * 解讀錯誤還好，有的駭客可以用這個bug進行
 * sql injection，竄改資料庫。
 * 
 * 為了避免這件事，需要用htmlspecialchars
 * 把引號事先轉為替代文字：&#039
 * 
 * 
 *******************************************/

function escape($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'utf-8');   //utf-8是中文編碼，不這樣容易亂碼
}