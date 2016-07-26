<?php
include_once 'functions.php';
/**
 * Catch all function handling all file related api requests
 * @param  [type] $app [description]
 * @return [type]      [description]
 */
function routeFileRequests($app){
  $app->post('/', function() use($app){
      $new_file = json_decode($app->request->getBody());
      //$response = createFile($new_file);
      echo json_encode($new_file);
  });
}

function createFile($file){
  try{
    $db = openDBConnection();
    $db->beginTransaction();
    $dt = new DateTime('now');
		$file->created = $dt->format('Y-m-d H:i:s');
    $sql_create_file = 'INSERT INTO files
                        ( file_id, start_date, end_date,
                          parish, activity, title, property_title, owner_id, receipt_id,
                          document_id, remark, createdBy, created)
                        VALUES (:file_id, :start_date, :end_date,
                                :parish, :activity, :title, :owner_id, :receipt_id
                                :document_id, :remark, :createdBy, :created)';
    $stmt = $db->prepare($sql_create_file);
    $stmt->execute(array(":file_id" => $file->file_id, //checked
                          ":start_date" => $file->start_date,
                          ":end_date" => $file->end_date,//checked
                          ":parish" => $file->parish, //checked
                          ":activity" => $file->activity, //checked
                          ":title" => $file.title, //checked
                          ":property_title" => $file.property_title,
                          ":owner_id" => 0,
                          ":receipt_id" => 0,
                          ":document_id" => 0,
                          ":remark" => $file.remarks, //checked
                          ":createdBy" => $file.createdBy, //checked
                          ":created"=> $file.created));
    $act_id = $db->lastInsertId();
    $stmt->closeCursor();

    $sql_create_owner = 'INSERT INTO owners (first_name, last_name, volume, folio, act_id)
                         VALUES (:first_name, :last_name, :volume, :folio, :act_id)';
    $stmt = $db->prepare($sql_create_owner);
    $stmt->execute(array(":first_name" => $file->first_name,
                          ":last_name" => $file->last_name,
                          ":volume" => $file->volume,
                          ":folio" => $file->folio,
                          ":act_id" => $act_id));
    $owner_id = $db->lastInsertId();
    $stmt->closeCursor();

    $sql_create_receipt = 'INSERT INTO receipts (seen, currency_id, amount, act_id)
                           VALUES (:seen, :currency_id, :amount, :act_id)';
    $stmt = $db->prepare($sql_create_receipt);
    $stmt->execute(array(":seen" => $file->receipt,
                          ":currency_id" => $file->currency_id,
                          ":amount" => $file->amount,
                          ":act_id" => $act_id));
    $receipt_id = $db->lastInsertId();
    $stmt->closeCursor();

    $sql_create_document = 'INSERT INTO documents
                            ( act_id, comp_agreement, sale_agreement, cot,
                              lease_agreement, map, property_tax, drawing,
                              surveyor_id, surveyor_report)
                           VALUES ( :act_id, :comp_agreement, :sale_agreement, :cot,
                             :lease_agreement, :map, :property_tax, :drawing,
                             :surveyor_id, :surveyor_report)';
    $stmt = $db->prepare($sql_create_document);
    $stmt->execute(array(":act_id" => $act_id,
                          ":comp_agreement" => $file->comp_agreement,
                          ":sale_agreement" => $file->sale_agreement,
                          ":cot" => $file->cot,
                          ":lease_agreement" => $file->lease_agreement,
                          ":map" => $file->map,
                          ":property_tax" => $file->property_tax,
                          ":drawing" => $file->drawing,
                          ":surveyor_id" => $file->surveyor_id,
                          ":surveyor_report" => $file->surveyor_report));
    $document_id = $db->lastInsertId();

    $sql_update_file = 'UPDATE files SET owner_id = :owner_id,
                        receipt_id = :receipt_id,
                        document_id = :document_id WHERE act_id = :act_id';
    $stmt = $db->prepare($sql_create_document);
    $stmt->execute(array(":owner_id" => $owner_id,
                          ":receipt_id" => $receipt_id,
                          ":document_id" => $document_id));
    $stmt->closeCursor();
    $db->commit();
    closeDBConnection( $db );
    $result = $act_id;
  }catch(PDOException $e){
    $db->rollBack();
    $result = '{"error":{"text":' .$e->getMessage(). '}}';
  }
  return $act_id;
}
?>
