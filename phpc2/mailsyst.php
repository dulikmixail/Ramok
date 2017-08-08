<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

class MailSystem
{
  function internalSendMailRegular($recipient, $subject, $message, $headers)
  {
    $headers=str_replace("\r","",$headers);
    $message=str_replace("\r","",$message);
    $headers=str_replace("\n",MailHeadersNewline,$headers);
    $message=str_replace("\n",MailMessageNewline,$message);
    return @mail($recipient,$subject,$message,$headers);
  }

  function internalSendMailDebug($sender, $recipient, $subject, $message, $headers)
  {
    global $fileSystem;
    $index=0;
    do {
      $filename=str_pad(++$index,8,"0",STR_PAD_LEFT);
      $filename=format(MailDebuggerFilename,$filename);
    } while($fileSystem->isFileExists($filename));
    $delimiter=str_pad("",78,"==--")."\r\n";
    $headers=str_replace("\r\n","\r\n         ",$headers);
    $content=
      "From:    $sender\r\n".
      "To:      $recipient\r\n".
      "Subject: $subject\r\n".
      "Headers: $headers\r\n";
    $content=$delimiter.$content.$delimiter.$message.$delimiter;
    return $fileSystem->saveFile($filename,$content);
  }

  function sendMail($sender, $recipient, $subject, $message, $html=false)
  {
    global $language;
    if(!preg_match(EmailAddressPattern,$recipient)) return false;
    $message=trim($message)."\r\n";
    $mimetype=$html?"text/html":"text/plain";
    $headers=
      "From: $sender\r\n".
      "Reply-To: $sender\r\n".
      "X-Mailer: PHPC/".phpcversion()."\r\n".
      "Content-Type: $mimetype; charset=$language[charset]";
    if(MailDebuggerEnabled)
      return $this->internalSendMailDebug($sender,$recipient,$subject,$message,$headers);
      else return $this->internalSendMailRegular($recipient,$subject,$message,$headers);
  }

  function sendMailAttachments($sender, $recipient, $subject, $message, $attachments=array(), $html=false)
  {
    global $language;
    if(!count($attachments))
      return $this->sendMail($sender,$recipient,$subject,$message,$html);
    if(!preg_match(EmailAddressPattern,$recipient)) return false;
    $mimetype=$html?"text/html":"text/plain";
    $uid="----------".phpcstrtoupper(uniqid(time()));
    $headers=
      "From: $sender\r\n".
      "Reply-To: $sender\r\n".
      "Mime-Version: 1.0\r\n".
      "X-Mailer: PHPC/".phpcversion()."\r\n".
      "Content-Type: multipart/mixed; boundary=\"$uid\"";
    $message=
      "--$uid\r\n".
      "Content-Type: $mimetype; charset=$language[charset]\r\n".
      "Content-Transfer-Encoding: 8bit\r\n\r\n".
      trim($message)."\r\n";
    foreach($attachments as $filename=>$attachment) {
      $filename=str_replace("\"","''",$filename);
      $message.=
        "\r\n--$uid\r\n".
        "Content-Type: application/octet-stream; name=\"$filename\"\r\n".
        "Content-Transfer-Encoding: base64\r\n".
        "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n".
        trim(chunk_split(base64_encode($attachment)))."\r\n";
    }
    if(MailDebuggerEnabled)
      return $this->internalSendMailDebug($sender,$recipient,$subject,$message,$headers);
      else return $this->internalSendMailRegular($recipient,$subject,$message,$headers);
  }
}

?>
