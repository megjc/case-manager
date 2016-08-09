<?php
include_once '../api/lib/functions.php';
/**
 * Catch all function handling all user related api requests
 * @param  [type] $app [description]
 * @return [type]      [description]
 */
function routeImportRequests($app){
  $app->get('/', function() use($app){
    $rows = array_map('str_getcsv', file('../imports/file.csv'));
    $header = array_shift($rows);
    $csv = array();
    foreach ($rows as $row) {
        $csv[] = array_combine($header, $row);
    }

    $len = count($csv);
    for($i = 0; $i< $len; $i++){
      importFile($csv[$i]);
    }
    //setResponseHeader($app);

  });
}

function importFile($file){
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
                        (file_id, start_date, end_date,
                          parish, act_type_id, title, property_title,
                          remarks, createdBy, created)
                        VALUES (:file_id, :start_date, :end_date,
                                :parish, :act_type_id, :title,
                                :property_title, :remarks,
                                :createdBy, NOW())';
    $stmt = $db->prepare($sql_create_file);
    $stmt->execute(array(":file_id" => trim($file->file_id), //checked
                          ":start_date" => $start_date,
                          ":end_date" => $end_date,//checked
                          ":parish" => $file->parish, //checked
                          ":act_type_id" => $file->activity, //checked
                          ":title" => trim(strtolower($file->title)), //checked
                          ":property_title" => trim(strtolower($file->property_title)),
                          ":remarks" => trim(strtolower($file->remarks)), //checked
                          ":createdBy" => $file->createdBy //checked
                          ));
    $acc_id = $db->lastInsertId();
    $stmt->closeCursor();

    $sql_create_owner = 'INSERT INTO owners (name, volume, folio, acc_id)
                         VALUES (:name, :volume, :folio, :acc_id)';
    $stmt = $db->prepare($sql_create_owner);

    //$len = count($file->owners);
    //while($len--){
      // $name = explode(" ", $file->owners[$len]->name);
      $stmt->execute(array(":name" => trim(strtolower($file->name)),
                            ":volume" => intval($file->volume),
                            ":folio" => intval($file->folio),
                            ":acc_id" => $acc_id));
    //}
    // $owner_id = $db->lastInsertId();
    $stmt->closeCursor();
    //
    $sql_create_receipt = 'INSERT INTO receipts (seen, currency_id, amount, type_id, acc_id)
                           VALUES (:seen, :currency_id, :amount, :type_id, :acc_id)';
    $stmt = $db->prepare($sql_create_receipt);
    //$len = count($file->receipts);
    //while($len--){
      if($file->receipts[$len]->seen === "yes") $seen = 1;
      else $seen = 0;
      $stmt->execute(array(":seen" => $seen,
                            ":currency_id" => $file->currency->id,
                            ":amount" => $file->amount,
                            ":type_id" => $file->type_id,
                            ":acc_id" => $acc_id));
    //}
    $stmt->closeCursor();
    // //
    // $sql_create_document = 'INSERT INTO documents
    //                         (acc_id, comp_agreement, sale_agreement, cot,
    //                           lease_agreement, map, surveyor_drawing, surveyor_report)
    //                        VALUES (:acc_id, :comp_agreement, :sale_agreement, :cot,
    //                          :lease_agreement, :map, :surveyor_drawing,
    //                          :surveyor_report)';
    // $stmt = $db->prepare($sql_create_document);
    // $stmt->execute(array(":acc_id" => $acc_id,
    //                       ":comp_agreement" => intval($file->comp_agreement),
    //                       ":sale_agreement" => intval($file->sale_agreement),
    //                       ":cot" => intval($file->cot),
    //                       ":lease_agreement" => intval($file->lease_agreement),
    //                       ":map" => intval($file->map),
    //                       ":surveyor_drawing" => intval($file->surveyor_drawing),
    //                       ":surveyor_report" => intval($file->surveyor_report)));
    $stmt->closeCursor();
    $db->commit();
    closeDBConnection( $db );
    $result = $acc_id;
  }catch(PDOException $e){
    $db->rollBack();
    $result = -1;
  }
  return $result;
}

?>
