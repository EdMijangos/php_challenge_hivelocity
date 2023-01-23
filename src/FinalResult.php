<?php

class FinalResult {
    function results($file) {
        $doc = fopen($file, "r");
        $headers = fgetcsv($doc);
        $records = [];

        // Constants
        define("ROW_ITEMS", 16);
        define("AMOUNT", 8);
        define("BANK_ACC", 6);
        define("BRANCH_CODE", 2);
        define("ID_START", 10);
        define("ID_END", 11);
        define("ACC_NAME", 7);
        define("BANK_CODE", 0);

        while(!feof($doc)) {
            $row = fgetcsv($doc);
            if(count($row) == ROW_ITEMS) {
                $amount = !$row[AMOUNT] || $row[AMOUNT] == "0" ? 0 : (float) $row[AMOUNT];
                $bank_acc = !$row[BANK_ACC] ? "Bank account number missing" : (int) $row[BANK_ACC];
                $branch_code = !$row[BRANCH_CODE] ? "Bank branch code missing" : $row[BRANCH_CODE];
                $e2e_id = !$row[ID_START] && !$row[ID_END] ? "End to end id missing" : $row[ID_START] . $row[ID_END];
                $new_record = [
                    "amount" => [
                        "currency" => $headers[0],
                        "subunits" => (int) ($amount * 100)
                    ],
                    "bank_account_name" => str_replace(" ", "_", strtolower($row[ACC_NAME])),
                    "bank_account_number" => $bank_acc,
                    "bank_branch_code" => $branch_code,
                    "bank_code" => $row[BANK_CODE],
                    "end_to_end_id" => $e2e_id,
                ];
                $records[] = $new_record;
            }
        }
        $records = array_filter($records);
        return [
            "filename" => basename($file),
            "document" => $doc,
            "failure_code" => $headers[1],
            "failure_message" => $headers[2],
            "records" => $records
        ];
    }
}

?>
