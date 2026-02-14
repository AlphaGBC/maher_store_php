<?php

require 'vendor/autoload.php'; // لضمان تحميل جميع المكتبات باستخدام Composer

// قم بتحميل مكتبة JWT هنا
use Firebase\JWT\JWT;


define("MB", 1048576);

function filterRequest($requestname)
{
    return  htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    if ($where == null) {
        $stmt = $con->prepare("SELECT  * FROM $table   ");
    } else {
        $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    } else {
        if ($count > 0) {
            return  array("status" => "success", "data" => $data);
        } else {
            return  array("status" => "failure");
        }
    }
}

function getData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        return $count;
    }
}


function getSpecificColumnData($table, $columns, $where = null, $values = null, $json = true)
{
    global $con; // الوصول إلى كائن الاتصال بقاعدة البيانات العام

    $data = array(); // تهيئة مصفوفة لتخزين البيانات

    // بناء الاستعلام بناءً على وجود شرط WHERE
    if ($where == null) {
        // إذا لم يكن هناك شرط WHERE، جلب الأعمدة المحددة من الجدول بأكمله
        $stmt = $con->prepare("SELECT $columns FROM $table");
    } else {
        // إذا كان هناك شرط WHERE، جلب الأعمدة المحددة بناءً على الشرط
        $stmt = $con->prepare("SELECT $columns FROM $table WHERE $where");
    }

    // تنفيذ الاستعلام مع القيم المربوطة (إذا وجدت)
    $stmt->execute($values);

    // جلب الصف الأول المطابق كصفيف ارتباطي
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // الحصول على عدد الصفوف المتأثرة بالاستعلام
    $count = $stmt->rowCount();

    // التعامل مع الإخراج بناءً على قيمة $json
    if ($json == true) {
        if ($count > 0) {
            // إذا تم العثور على بيانات، طباعة استجابة JSON بنجاح والبيانات
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            // إذا لم يتم العثور على بيانات، طباعة استجابة JSON بالفشل
            echo json_encode(array("status" => "failure"));
        }
        // في هذه الحالة، لا تعيد الدالة أي شيء إضافي بعد طباعة JSON
    } else {
        if ($count > 0) {
            // إذا تم العثور على بيانات و $json خاطئ، إرجاع مصفوفة بنجاح والبيانات
            return array("status" => "success", "data" => $data);
        } else {
            // إذا لم يتم العثور على بيانات و $json خاطئ، إرجاع مصفوفة بالفشل
            return array("status" => "failure");
        }
    }
}




function insertData($table, $data, $json = true)
{
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}


function updateData($table, $data, $where, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` =  ? ";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}


function edititemsData($table, $data, $where, $json = true)
{
    global $con;
    $cols = [];
    $vals = [];

    foreach ($data as $key => $val) {
        $cols[] = "`$key` = ?";
        $vals[] = $val;
    }

    $sql = "UPDATE `$table` SET " . implode(', ', $cols) . " WHERE $where";

    try {
        $stmt = $con->prepare($sql);
        $stmt->execute($vals);
        $count = $stmt->rowCount();
        if ($json) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                "status" => $count > 0 ? "success" : "failure",
               
            ]);
        }
        return $count;
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage(),
            "sql"     => $sql,
            "params"  => $vals
        ]);
        exit;
    }
}



function deleteData($table, $where, $json = true)
{
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function imageUpload($dir, $imageRequest)
{
    global $msgError;
    if (isset($_FILES[$imageRequest])) {
        $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
        $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
        $imagesize  = $_FILES[$imageRequest]['size'];
        $allowExt   = array("jpg", "png", "gif", "mp3", "pdf" , "svg");
        $strToArray = explode(".", $imagename);
        $ext        = end($strToArray);
        $ext        = strtolower($ext);

        if (!empty($imagename) && !in_array($ext, $allowExt)) {
            $msgError = "EXT";
        }
        if ($imagesize > 2 * MB) {
            $msgError = "size";
        }
        if (empty($msgError)) {
            move_uploaded_file($imagetmp,  $dir . "/" . $imagename);
            return $imagename;
        } else {
            return "fail";
        }
    }else {
        return 'empty' ; 
    }
}



function deleteFile($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkAuthenticate()
{
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "" ||  $_SERVER['PHP_AUTH_PW'] != "") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }

    // End 
}


function   printFailure($message = "none")
{
    echo     json_encode(array("status" => "failure", "message" => $message));
}
function   printSuccess($message = "none")
{
    echo     json_encode(array("status" => "success", "message" => $message));
}

function result($count)
{
    if ($count > 0) {
        printSuccess();
    } else {
        printFailure();
    }
}

// function sendsms ($phone, $body)
// {
//     try {


//  $account_sid = 'AC253e3b256232bb7b028a90b31aa96a5a';
//  $auth_token = '120d8a115100ca43c0f8f3b76b2545e3';

// $twilio_number = "+12185229181";

// $client = new Client($account_sid, $auth_token);
// $client->messages->create(

//     "+963$phone",
//     array(
//         'from' => $twilio_number,
//         'body' =>  $body
//     )
// );
// } catch (Exception $e) {
//   echo 'Error: ' . $e->getMessage();
//   }
// }

function sendEmail($to, $title, $body)
{
    $header = "From: support@sahlhastore.com " . "\n" . "CC: sahlhastore@gmail.com";
    mail($to, $title, $body, $header);
}



function getAccessToken($credentials) {
    $now = time();
    $payload = [
        'iss' => $credentials['client_email'],
        'sub' => $credentials['client_email'],
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
    ];

    $jwt = JWT::encode($payload, $credentials['private_key'], 'RS256');

    $response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
        ],
    ]));

    return json_decode($response, true)['access_token'];
}

// قراءة بيانات الاعتماد من ملف JSON
$credentials = json_decode(file_get_contents('https://sahlhastore.com/maher_store_php/maher-store-55aad-firebase-adminsdk-fbsvc-64ac7fd637.json'), true);
$accessToken = getAccessToken($credentials);

function sendFCM($title, $message, $topic, $pageid, $pagename, $accessToken, $imageUrl = null){
    $url = 'https://fcm.googleapis.com/v1/projects/maher-store-55aad/messages:send';

    $notification = [
        'title' => $title,
        'body'  => $message,
    ];
    // فقط إذا وُجد رابط
    if ($imageUrl) {
        $notification['image'] = $imageUrl;
    }

    $fields = [
        'message' => [
            'topic'        => $topic,
            'notification' => $notification,
            'data'         => [
                'pageid'   => $pageid,
                'pagename' => $pagename
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => json_encode($fields)
    ]);

    $result = curl_exec($ch);
    if ($result === false) {
        error_log('FCM error: ' . curl_error($ch));
    }
    curl_close($ch);

    return $result;
}



function insertNotify($title, $body, $userid, $topic, $pageid, $pagename){
    global $con;
    $stmt = $con->prepare("INSERT INTO notification(  notification_title, notification_body, notification_userid) VALUES (? , ? , ?)");
    $stmt->execute(array($title, $body, $userid));
    global $accessToken;
    sendFCM($title, $body, $topic, $pageid, $pagename, $accessToken);
    $count = $stmt->rowCount();
    return $count;
}

function sendNotificationBulk($title, $body, $topic, $pageid, $page2id, $pagename,  $imageUrl = ''){
    global $con;

    // 1) إدراج دفعة لكل المستخدمين
    $stmt = $con->prepare(
        "INSERT INTO notification
           (notification_title, notification_body, notification_userid, notification_items, notification_categoriesa, notification_image)
         SELECT
           :title,
           :body,
           users_id,
           :items,
           :categoriesa,
           :imageUrl
         FROM users"
    );
    $stmt->execute([
        ':title'    => $title,
        ':body'     => $body,
        ':items'     => $pageid,
        ':categoriesa'     => $page2id,
        ':imageUrl' => $imageUrl
    ]);
    $insertedCount = $stmt->rowCount();
global $accessToken;
    // 2) إرسال دفعة واحدة عبر FCM
    $fcmResult = sendFCM(
        $title,
        $body,
        $topic,
        $pageid,
        $pagename,
        $accessToken,
        $imageUrl
    );

    // 3) إرجاع عدد الصفوف المُدخلة (أو false إذا فشل الإرسال)
    return ($fcmResult !== false) ? $insertedCount : false;
}


