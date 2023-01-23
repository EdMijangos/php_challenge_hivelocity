<?php

class FinalResult {
    function results($file) {
        $doc = fopen($file, "r");
        $headers = fgetcsv($doc);
        $records = [];
        while(!feof($doc)) {
            $row = fgetcsv($doc);
            if(count($row) == 16) {
                $amount = !$row[8] || $row[8] == "0" ? 0 : (float) $row[8];
                $bank_acc = !$row[6] ? "Bank account number missing" : (int) $row[6];
                $bank_code = !$row[2] ? "Bank branch code missing" : $row[2];
                $e2e_id = !$row[10] && !$row[11] ? "End to end id missing" : $row[10] . $row[11];
                $new_record = [
                    "amount" => [
                        "currency" => $headers[0],
                        "subunits" => (int) ($amount * 100)
                    ],
                    "bank_account_name" => str_replace(" ", "_", strtolower($row[7])),
                    "bank_account_number" => $bank_acc,
                    "bank_branch_code" => $bank_code,
                    "bank_code" => $row[0],
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
