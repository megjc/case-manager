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
    $count = 0;
    for($i = 0; $i< $len; $i++){
      $result = importFile($csv[$i]);
      if($result > 0) $count++;
    }
    $message = $count." of ".$len." files imported.";
    setResponseHeader($app);
    echo json_encode($message);
  });
}
/**
 * Inserts a file into the database
 * @param  [type] $file [description]
 * @return [type]       [description]
 */
function importFile($file){
  try{
    $db = openDBConnection();
    $db->beginTransaction();
    $temp = date_create($file["start_date"]);
    $start_date = date_format($temp, "Y-m-d H:i:s");
    $temp = date_create($file["end_date"]);
    $end_date = date_format($temp, "Y-m-d H:i:s");
    $dt = date("Y-m-d H:i:s");
    $sql_create_file = 'INSERT INTO files (acc_id_man, start_date, end_date,
                                          title, property_title, parish,
                                          act_type_id, remarks, createdBy,
                                          created, file_id, verified, verifiedBy,
                                          box_ref)
                        VALUES (:acc_id_man, :start_date, :end_date, :title,
                                :property_title, :parish, :act_type_id, :remarks,
                                :createdBy, :created, :file_id, :verified,
                                :verifiedBy, :box_ref)';

    $stmt = $db->prepare($sql_create_file);

    $stmt->execute(array(":acc_id_man" => trim($file["acc_id_man"]),
                          ":start_date" => $dt,
                          ":end_date" => $dt,
                          ":title" => trim(strtolower($file["title"])),
                          ":property_title" => trim(strtolower($file["property_title"])),
                          ":parish" => $file["parish"],
                          ":act_type_id" => $file["act_type_id"],
                          ":remarks" => trim(strtolower($file["remarks"])),
                          ":createdBy" => $file["createdBy"],
                          ":created" => $dt,
                          ":file_id" => $file["file_id"],
                          ":verified" => $file["verified"],
                          ":verifiedBy" => $file["verifiedBy"],
                          ":box_ref" => $file["box_ref"]));
    $acc_id = $db->lastInsertId();
    $stmt->closeCursor();

    $sql_create_owner = 'INSERT INTO owners (name, volume, folio, acc_id)
                         VALUES (:name, :volume, :folio, :acc_id)';
    $stmt = $db->prepare($sql_create_owner);

    $stmt->execute(array(":name" => trim(strtolower($file["name"])),
                            ":volume" => $file["volume"],
                            ":folio" => $file["folio"],
                            ":acc_id" => $acc_id));
    $stmt->closeCursor();

    $sql_create_receipt = 'INSERT INTO receipts
                          (seen, currency_id, amount, type_id, acc_id)
                           VALUES (:seen, :currency_id, :amount, :type_id,
                                    :acc_id)';

    $stmt = $db->prepare($sql_create_receipt);

    $stmt->execute(array(":seen" => $file["seen"],
                            ":currency_id" => $file["currency_id"],
                            ":amount" => $file["amount"],
                            ":type_id" => $file["type_id"],
                            ":acc_id" => $acc_id));
    $stmt->closeCursor();

    $sql_create_document = 'INSERT INTO documents
                            ( acc_id, comp_agreement, sale_agreement, cot,
                              lease_agreement, map, surveyor_drawing,
                              surveyor_report)
                           VALUES (:acc_id, :comp_agreement, :sale_agreement, :cot,
                             :lease_agreement, :map, :surveyor_drawing,
                             :surveyor_report)';

    $stmt = $db->prepare($sql_create_document);

    $stmt->execute(array(":acc_id" => $acc_id,
                          ":comp_agreement" => intval($file["comp_agreement"]),
                          ":sale_agreement" => intval($file["sale_agreement"]),
                          ":cot" => intval($file["cot"]),
                          ":lease_agreement" => intval($file["lease_agreement"]),
                          ":map" => intval($file["map"]),
                          ":surveyor_drawing" => intval($file["surveyor_drawing"]),
                          ":surveyor_report" => intval($file["surveyor_report"])
                        ));
    $stmt->closeCursor();
    $db->commit();
    closeDBConnection( $db );
    $result = $acc_id;
  }catch(PDOException $e){
    $db->rollBack();
    $result = $e->getMessage();
  }
  return $result;
}
?>
