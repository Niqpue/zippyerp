<?php

namespace ZippyERP\ERP\Pages;

//страница  для  загрузки  файла  
class LoadFile extends \Zippy\Html\WebPage
{

    public function __construct($file_id)
    {
        if (!is_numeric($file_id))
            die;

        $user = \ZippyERP\System\System::getUser();
        if ($user->user_id == 0) {
            die;
        }

        $file = \ZippyERP\ERP\Helper::loadFile($file_id);
        if ($file == null)
            die;

        $type = "";
        $pos = strrpos($file['filename'], '.');
        if ($pos !== false) {
            $type = substr($file['filename'], $pos + 1);
        }
        $size = strlen($file['filedata']);
        if ($size > 0) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file['filename']);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . $size);

            flush();
            echo $file['filedata'];
        }
        die;
    }

}

?>
