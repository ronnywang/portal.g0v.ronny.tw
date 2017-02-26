# portal.g0v.ronny.tw
http://portal.g0v.ronny.tw/ 財政部關貿署進出口資料網站程式碼

環境建立方式
------------
* 先架設好 MySQL ，並準備好資料庫帳號密碼資料庫名稱
* 將 webdata/config.sample.php 複製成 webdata/config.php ，並把帳號密碼改進去
* 執行 php webdata/scripts/init-db.php ，將資料表建立起來

測試方式
--------
* 執行 php -S 0:3000 index.php ，並開瀏覽器看 http://localhost:3000/ 是否有內容出現

資料匯入方式
------------
* 商品代碼匯入
** 至 http://ronnywang-twportal.s3-website-ap-northeast-1.amazonaws.com/ 下載 good_code.csv 下載商品列表
** 執行 php webdata/scripts/import-id.php good_code.csv 匯入商品列表
* 進出口資料匯入
** 至 http://ronnywang-twportal.s3-website-ap-northeast-1.amazonaws.com/ 下載 92.tgz ~ 104.tgz 以及 good_in, good_out, good_rein, good_reout 內容
** 若是整年資料的 tgz，需要將他解壓縮開來，解壓縮之後會到 good_in, good_out, good_rein, good_reout 等資料夾
** 執行 php webdata/scripts/import.php (完整路徑)good_*/*  記得路徑內要有包含 good_in, good_out, good_rein, good_reout 等字串，才能讓他匯入到正確的位置
* 進出口資料的爬蟲程式位置在 https://github.com/ronnywang/portal.sw.nat.gov.tw/blob/master/goods/src/crawler_stats.php 


程式碼授權方式
--------------
以上程式碼以 BSD License 授權
