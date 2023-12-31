<?php

class InitiateCommission {

  function __construct($userid,$userReferedBy,$transactionType,$transactionMsg,$isAllowed){
    $this->userid = $userid;
    $this->userReferedBy = $userReferedBy;
    $this->transactionType = $transactionType;
    $this->transactionMsg = $transactionMsg;
    $this->isAllowed = $isAllowed;
  }

  function SendBonus($conn){
    $returnVal = "false";
    
    if($this->isAllowed==true && $this->userReferedBy!=""){

        $LEVEL_1_BONUS_RETURN = 2;
        $LEVEL_2_BONUS_RETURN = 2;
        $LEVEL_3_BONUS_RETURN = 2;
        $LEVEL_4_BONUS_RETURN = 2;
        $LEVEL_5_BONUS_RETURN = 3;
        
        $LEVEL_1_DIRECT_BONUS = 150;
        $LEVEL_2_DIRECT_BONUS = 40;
        $LEVEL_3_DIRECT_BONUS = 30;
        $LEVEL_4_DIRECT_BONUS = 20;
        $LEVEL_5_DIRECT_BONUS = 10;
          
        // getting current date & times
        date_default_timezone_set("Asia/Kolkata");
        $curr_date_time = date("d-m-Y h:i a");
        

        // level 1 commision
        $search_sql = "SELECT user_balance,user_refered_by FROM usersdata WHERE uniq_id='{$this->userReferedBy}' AND account_level >= 2 ";
        $search_query = mysqli_query($conn, $search_sql);

        if (mysqli_num_rows($search_query) > 0) {
                
            $search_res_data = mysqli_fetch_assoc($search_query);
            $level1_refered_by = $search_res_data["user_refered_by"];
            $level1_user_balance = $search_res_data["user_balance"];
                  
            $level_1_bonus = number_format($level1_user_balance + $LEVEL_1_BONUS_RETURN, 2, ".", "");
            
            // save comission data
            if($this->transactionMsg!="null"){
               $extraMsg = $this->transactionMsg.' 1';
               $level_1_bonus = number_format($level1_user_balance + $LEVEL_1_DIRECT_BONUS, 2, ".", "");
               $transaction_bonus = $LEVEL_1_DIRECT_BONUS;
            }else{
               $extraMsg = "Level 1";
               $transaction_bonus = $LEVEL_1_BONUS_RETURN;
            }
                  
            $level1_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?");
            $level1_update_sql->bind_param("ss",$level_1_bonus,$this->userReferedBy);
            $level1_update_sql->execute();
              
            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
            $insert_sql->bind_param("ssssss",
                $this->userReferedBy,
                $this->userid,
                $this->transactionType,
                $transaction_bonus,
                $extraMsg,
                $curr_date_time
            );
            $insert_sql->execute();
               
            
            $returnVal = "true";

            if ($level1_refered_by != "") {
                    
                // level 2 commision
                $level2_sql = "SELECT user_balance,user_refered_by FROM usersdata WHERE uniq_id='{$level1_refered_by}' AND account_level >= 2 ";
                $level2_query = mysqli_query($conn, $level2_sql);

                if (mysqli_num_rows($level2_query) > 0) {
                    
                    $level2_res_data = mysqli_fetch_assoc($level2_query);
                    $level2_refered_by = $level2_res_data["user_refered_by"];
                    $level2_user_balance = $level2_res_data["user_balance"];
 
                    $level_2_bonus = number_format( $level2_user_balance + $LEVEL_2_BONUS_RETURN,2,".","");
                    
                    // save comission data
                    if($this->transactionMsg!="null"){
                      $extraMsg = $this->transactionMsg.' 2';
                      
                      $level_2_bonus = number_format($level2_user_balance + $LEVEL_2_DIRECT_BONUS, 2, ".", "");
                      $transaction_bonus = $LEVEL_2_DIRECT_BONUS;
                     }else{
                      $extraMsg = "Level 2";
                      $transaction_bonus = $LEVEL_2_BONUS_RETURN;
                    }
                    
                    $level2_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?");
                    $level2_update_sql->bind_param("ss",$level_2_bonus,
                        $level1_refered_by);
                    $level2_update_sql->execute();

                     $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                     $insert_sql->bind_param("ssssss",$level1_refered_by,
                        $this->userid,
                        $this->transactionType,
                        $transaction_bonus,
                        $extraMsg,
                        $curr_date_time);
                     $insert_sql->execute();

                     if ($level2_refered_by != "") {
                        
                        // level 3 commision
                        
                        $level3_sql = "SELECT user_balance,user_refered_by FROM usersdata WHERE uniq_id='{$level2_refered_by}' AND account_level >= 2 ";
                        $level3_query = mysqli_query($conn, $level3_sql);

                        if (mysqli_num_rows($level3_query) > 0) {
                            
                            $level3_res_data = mysqli_fetch_assoc($level3_query);
                            $level3_refered_by = $level3_res_data["user_refered_by"];
                            $level3_user_balance = $level3_res_data["user_balance"];
                                
                            $level_3_bonus = number_format($level3_user_balance + $LEVEL_3_BONUS_RETURN,2,
                                ".","");
                                
                            
                            // save comission data
                            if($this->transactionMsg!="null"){
                              $extraMsg = $this->transactionMsg.' 3';
                              
                              $level_3_bonus = number_format($level3_user_balance + $LEVEL_3_DIRECT_BONUS, 2, ".", "");
                              $transaction_bonus = $LEVEL_3_DIRECT_BONUS;
                            }else{
                              $extraMsg = "Level 3";
                              $transaction_bonus = $LEVEL_3_BONUS_RETURN;
                            }
                            
                            
                            $level3_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?"
                            );
                            $level3_update_sql->bind_param("ss",$level_3_bonus,
                                $level2_refered_by);
                            $level3_update_sql->execute();
                            

                            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                            $insert_sql->bind_param("ssssss",$level2_refered_by,
                                $this->userid,
                                $this->transactionType,
                                $transaction_bonus,
                                $extraMsg,
                                $curr_date_time);
                            $insert_sql->execute();
                            
                            if ($level3_refered_by != "") {
                        
                        // level 4 commision
                        
                        $level4_sql = "SELECT user_balance,user_refered_by FROM usersdata WHERE uniq_id='{$level3_refered_by}' AND account_level >= 2 ";
                        $level4_query = mysqli_query($conn, $level4_sql);

                        if (mysqli_num_rows($level4_query) > 0) {
                            
                            $level4_res_data = mysqli_fetch_assoc($level4_query);
                            $level4_refered_by = $level4_res_data["user_refered_by"];
                            $level4_user_balance = $level4_res_data["user_balance"];
                                
                            $level_4_bonus = number_format($level4_user_balance + $LEVEL_4_BONUS_RETURN,2,
                                ".","");
                                
                                
                            // save comission data
                            if($this->transactionMsg!="null"){
                              $extraMsg = $this->transactionMsg.' 4';
                              
                              $level_4_bonus = number_format($level4_user_balance + $LEVEL_4_DIRECT_BONUS, 2, ".", "");
                              $transaction_bonus = $LEVEL_4_DIRECT_BONUS;
                            }else{
                              $extraMsg = "Level 4";
                              $transaction_bonus = $LEVEL_4_BONUS_RETURN;
                            }
                            
                                
                            $level4_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?"
                            );
                            $level4_update_sql->bind_param("ss",$level_4_bonus,
                                $level3_refered_by);
                            $level4_update_sql->execute();
                            

                            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                            $insert_sql->bind_param("ssssss",$level3_refered_by,
                                $this->userid,
                                $this->transactionType,
                                $transaction_bonus,
                                $extraMsg,
                                $curr_date_time);
                            $insert_sql->execute();
                            
                            if ($level4_refered_by != "") {
                        
                        // level 5 commision
                        
                        $level5_sql = "SELECT user_balance,user_refered_by FROM usersdata WHERE uniq_id='{$level4_refered_by}' AND account_level >= 2 ";
                        $level5_query = mysqli_query($conn, $level5_sql);

                        if (mysqli_num_rows($level5_query) > 0) {
                            
                            $level5_res_data = mysqli_fetch_assoc($level5_query);
                            $level5_user_balance = $level5_res_data["user_balance"];
                                
                            $level_5_bonus = number_format($level5_user_balance + $LEVEL_5_BONUS_RETURN,2,
                                ".","");
                                
                                
                            // save comission data
                            if($this->transactionMsg!="null"){
                              $extraMsg = $this->transactionMsg.' 5';
                              
                              $level_5_bonus = number_format($level5_user_balance + $LEVEL_5_DIRECT_BONUS, 2, ".", "");
                              $transaction_bonus = $LEVEL_5_DIRECT_BONUS;
                            }else{
                              $extraMsg = "Level 5";
                              $transaction_bonus = $LEVEL_5_BONUS_RETURN;
                            }
                            
                                
                            $level5_update_sql = $conn->prepare("UPDATE usersdata SET user_balance = ? WHERE uniq_id = ?"
                            );
                            $level5_update_sql->bind_param("ss",$level_5_bonus,
                                $level4_refered_by);
                            $level5_update_sql->execute();


                            $insert_sql = $conn->prepare("INSERT INTO othertransactions(user_id,receive_from,type,amount,extra_msg,date_time) VALUES(?,?,?,?,?,?)");
                            $insert_sql->bind_param("ssssss",$level4_refered_by,
                                $this->userid,
                                $this->transactionType,
                                $transaction_bonus,
                                $extraMsg,
                                $curr_date_time);
                            $insert_sql->execute();
                             
                            
                        }
                     }
                             
                            
                        }
                     }
                             
                            
                        }
                     }
                     
                    
                }
            }
          
        }
    }
    
    return $returnVal;

  }


  function __destruct(){
  }
}