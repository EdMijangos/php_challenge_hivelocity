<?php

class FinalResult {
    // Constants
    const ROW_ITEMS = 16;
    const AMOUNT = 8;
    const BANK_ACC = 6;
    const BRANCH_CODE = 2;
    const ID_START = 10;
    const ID_END = 11;
    const ACC_NAME = 7;
    const BANK_CODE = 0;

    function results($file) {
        if (!file_exists($file)) {
            throw new Exception("Cannot find file.");
        }
        $doc = fopen($file, "r");
        $headers = fgetcsv($doc);
        $records = [];
        while(!feof($doc)) {
            $row = fgetcsv($doc);
            try {
                $records[] = $this->make_record($row, $headers[0]);
            } 
            catch (Exception $e) {
                $records[] = $e->getMessage();
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

    function make_record($row, $currency) {
        if(count($row) < self::ROW_ITEMS) {
            throw new Exception("Could not process entry. Some data might be missing.");
        }
        $amount = !$row[self::AMOUNT] || $row[self::AMOUNT] == "0" ? 0 : (float) $row[self::AMOUNT];
        $bank_acc = !$row[self::BANK_ACC] ? "Bank account number missing" : (int) $row[self::BANK_ACC];
        $branch_code = !$row[self::BRANCH_CODE] ? "Bank branch code missing" : $row[self::BRANCH_CODE];
        $e2e_id = !$row[self::ID_START] && !$row[self::ID_END] ? 
            "End to end id missing" : $row[self::ID_START] . $row[self::ID_END];
        $new_record = [
            "amount" => [
                "currency" => $currency,
                "subunits" => (int) ($amount * 100)
            ],
            "bank_account_name" => str_replace(" ", "_", strtolower($row[self::ACC_NAME])),
            "bank_account_number" => $bank_acc,
            "bank_branch_code" => $branch_code,
            "bank_code" => $row[self::BANK_CODE],
            "end_to_end_id" => $e2e_id,
        ];
        return $new_record;
    }
}

?>
