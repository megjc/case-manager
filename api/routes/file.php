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
      $response = createFile($new_file);
      setResponseHeader($app);
      echo json_encode($response);
  });

  $app->get('/', function() use($app){
      $response = getFiles();
      setResponseHeader($app);
      echo json_encode($response);
  });
}
/**
 * Creates a file from the body of a post request
 * @param  [type] $file [description]
 * @return [type]       [description]
 */
function createFile($file){
  try{
    $db = openDBConnection();
    $db->beginTransaction();
    $dt = new DateTime('now');
		$file->created = $dt->format('Y-m-d H:i:s');
    $temp = date_create($file->start_date);
    $start_date = date_format($temp, "Y-m-d H:i:s");
    $temp = date_create($file->end_date);
    $end_date = date_format($temp,"Y-m-d H:i:s");
    $sql_create_file = 'INSERT INTO files
                        ( file_id, start_date, end_date,
                          parish, act_type_id, title, property_title, owner_id,
                          receipt_id, document_id, remarks, createdBy, created)
                        VALUES (:file_id, :start_date, :end_date,
                                :parish, :act_type_id, :title, :property_title,
                                :owner_id, :receipt_id,:document_id, :remarks,
                                :createdBy, NOW())';
    $stmt = $db->prepare($sql_create_file);
    $stmt->execute(array(":file_id" => $file->file_id, //checked
                          //":start_date" => $file->start_date,
                          ":start_date" => $start_date,
                          ":end_date" => $end_date,//checked
                          ":parish" => $file->parish, //checked
                          ":act_type_id" => $file->activity, //checked
                          ":title" => $file->title, //checked
                          ":property_title" => $file->property_title,
                          ":owner_id" => 0,
                          ":receipt_id" => 0,
                          ":document_id" => 0,
                          ":remarks" => $file->remarks, //checked
                          ":createdBy" => $file->createdBy //checked
                          ));
    $acc_id = $db->lastInsertId();
    $stmt->closeCursor();

    // $sql_create_owner = 'INSERT INTO owners (first_name, last_name, volume, folio, act_id)
    //                      VALUES (:first_name, :last_name, :volume, :folio, :act_id)';
    // $stmt = $db->prepare($sql_create_owner);
    // $stmt->execute(array(":first_name" => $file->first_name,
    //                       ":last_name" => $file->last_name,
    //                       ":volume" => $file->volume,
    //                       ":folio" => $file->folio,
    //                       ":acc_id" => $acc_id));
    // $owner_id = $db->lastInsertId();
    // $stmt->closeCursor();
    //
    // $sql_create_receipt = 'INSERT INTO receipts (seen, currency_id, amount, act_id)
    //                        VALUES (:seen, :currency_id, :amount, :act_id)';
    // $stmt = $db->prepare($sql_create_receipt);
    // $stmt->execute(array(":seen" => $file->receipt,
    //                       ":currency_id" => $file->currency_id,
    //                       ":amount" => $file->amount,
    //                       ":acc_id" => $acc_id));
    // $receipt_id = $db->lastInsertId();
    // $stmt->closeCursor();
    //
    // $sql_create_document = 'INSERT INTO documents
    //                         ( acc_id, comp_agreement, sale_agreement, cot,
    //                           lease_agreement, map, property_tax,
    //                           surveyor_drawing, surveyor_report)
    //                        VALUES ( :acc_id, :comp_agreement, :sale_agreement, :cot,
    //                          :lease_agreement, :map, :property_tax, :surveyor_drawing,
    //                          :surveyor_report)';
    // $stmt = $db->prepare($sql_create_document);
    // $stmt->execute(array(":acc_id" => $acc_id,
    //                       ":comp_agreement" => $file->comp_agreement,
    //                       ":sale_agreement" => $file->sale_agreement,
    //                       ":cot" => $file->cot,
    //                       ":lease_agreement" => $file->lease_agreement,
    //                       ":map" => $file->map,
    //                       ":property_tax" => $file->property_tax,
    //                       ":surveyor_drawing" => $file->surveyor_drawing,
    //                       ":surveyor_report" => $file->surveyor_report));
    // $document_id = $db->lastInsertId();
    //
    // $sql_update_file = 'UPDATE files SET owner_id = :owner_id,
    //                     receipt_id = :receipt_id,
    //                     document_id = :document_id WHERE acc_id = :acc_id';
    // $stmt = $db->prepare($sql_create_document);
    // $stmt->execute(array(":owner_id" => $owner_id,
    //                       ":receipt_id" => $receipt_id,
    //                       ":document_id" => $document_id));
    // $stmt->closeCursor();
    $db->commit();
    closeDBConnection( $db );
    $result = $acc_id;
  }catch(PDOException $e){
    $db->rollBack();
    $result = '{"error":{"text":' .$e->getMessage(). '}}';
  }
  return $result;
}
/**
 * Retrieves all files from the database
 * @return [type] [description]
 */
function getFiles(){
  $sql = "SELECT f.acc_id, f.file_id, f.owner_id, p.title as parish,
          u.first_name as user_first_name, u.last_name as user_last_name
          FROM files as f
          INNER JOIN users as u ON f.createdBy = u.id
          INNER JOIN parishes as p ON f.parish = p.id";
  try{
    $db = openDBConnection();
    $stmt = $db->prepare( $sql );
    $stmt->execute();
    $result = $stmt->fetchAll( PDO::FETCH_OBJ );
    closeDBConnection( $db );
  }catch(PDOException $e){
    $result = '{"error":{"text":' .$e->getMessage(). '}}';
  }
  return $result;
}
?>
