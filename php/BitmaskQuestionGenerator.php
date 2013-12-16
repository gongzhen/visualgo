<?php
	class BitmaskQuestionGenerator implements QuestionGeneratorInterface{
		
		public function seedRng($seed){
			$this->rngSeed = $seed;
			mt_srand($rngSeed);
		}
	
		public function removeSeed(){
			$this->rngSeed = NULL;
			mt_srand();
		}
		
		//constructor
		public function __construct(){
		}
		
		//interface functions
		public function generateQuestion($amt){
			$questions = array();
			
			for($i = 0; $i < $amt; $i++){
				$potentialQuestions = array();
	
				$potentialQuestions[] = $this->generateQuestionBitOperations();
				$potentialQuestions[] = $this->generateQuestionConversion();
				$potentialQuestions[] = $this->generateQuestionNumberOn();
				$potentialQuestions[] = $this->generateQuestionLSOne();
	
				$questions[] = $potentialQuestions[mt_rand(0, count($potentialQuestions) - 1)];
			}
			return $questions;
		}
		
		public function checkAnswer($qObj, $userAns){
			if($qObj->qType == QUESTION_TYPE_OPERATION) return $this->checkAnswerBitOperations($qObj, $userAns);
			else if ($qObj->qType == QUESTION_TYPE_CONVERT) return $this->checkAnswerConversion($qObj, $userAns);
			else if ($qObj->qType == QUESTION_TYPE_NUMBER_ON) return $this->checkAnswerNumberOn($qObj, $userAns);
			else if ($qObj->qType == QUESTION_TYPE_LSONE) return $this->checkAnswerLSOne($qObj, $userAns);
			else return false;
		}
		
		//each question type generator and checker
		//AND/OR/XOR
		public function generateQuestionBitOperations() {
			$intval = mt_rand(0,63);
			$j = mt_rand(0,5);
			$subtype = mt_rand(0,2);
			$subtypeArr = array(QUESTION_SUB_TYPE_AND, QUESTION_SUB_TYPE_OR, QUESTION_SUB_TYPE_XOR);
			
			$qObj = new QuestionObject();
			$qObj->qTopic = QUESTION_TOPIC_BITMASK;
			$qObj->qType = QUESTION_TYPE_OPERATION;
			$qObj->qParams = array("value" => $intval,"subtype" => $subtypeArr[$subtype], "shiftAmt" => $j);
			$qObj->aType = ANSWER_TYPE_FILL_BLANKS;
			$qObj->aAmt = ANSWER_AMT_ONE;
			$qObj->ordered = false;
			$qObj->allowNoAnswer = false;
			$qObj->graphState = array("vl" => array(), "el" => array()); //empty graph
		
			return $qObj;
		}
		
		public function checkAnswerBitOperations($qObj, $userAns) {
			$intval = $qObj->qParams["value"];
			$j = $qObj->qParams["shiftAmt"];
			$ans;
			if($qObj->qParams["subtype"] == QUESTION_SUB_TYPE_AND) $ans = $intval & (1 << $j);
			else if($qObj->qParams["subtype"] == QUESTION_SUB_TYPE_OR) $ans = $intval | (1 << $j);
			else if($qObj->qParams["subtype"] == QUESTION_SUB_TYPE_XOR) $ans = $intval ^ (1 << $j);
			return ($userAns[0] == $ans);
		}
		
		//binary <--> decimal conversion
		public function generateQuestionConversion() {
			$val = mt_rand(0,63);
			$whichway = mt_rand(0,1);
			$subtypeArr = array(QUESTION_SUB_TYPE_BINARY, QUESTION_SUB_TYPE_DECIMAL);
			if($whichway == 0) { //binary to decimal question
				$val = intval(base_convert((string)$val, 10, 2)); //convert to binary
			}
			
			$qObj = new QuestionObject();
			$qObj->qTopic = QUESTION_TOPIC_BITMASK;
			$qObj->qType = QUESTION_TYPE_CONVERT;
			$qObj->qParams = array("value" => $val,"fromBase" => $subtypeArr[$whichway], "toBase" => $subtypeArr[1-$whichway]);
			$qObj->aType = ANSWER_TYPE_FILL_BLANKS;
			$qObj->aAmt = ANSWER_AMT_ONE;
			$qObj->ordered = false;
			$qObj->allowNoAnswer = false;
			$qObj->graphState = array("vl" => array(), "el" => array()); //empty graph
		
			return $qObj;
		}
		
		public function checkAnswerConversion($qObj, $userAns) {
			$val = $qObj->qParams["value"];
			$toBase = $qObj->qParams["toBase"];
			$ans;
			if($qObj->qParams["toBase"] == QUESTION_SUB_TYPE_BINARY) $ans = intval(base_convert((string)$val, 10, 2));
			else if($qObj->qParams["toBase"] == QUESTION_SUB_TYPE_DECIMAL) $ans = intval(base_convert((string)$val, 2, 10));
			return ($userAns[0] == $ans);
		}
		
		//popcount
		public function generateQuestionNumberOn() {
			$val = mt_rand(0,63);
			
			$qObj = new QuestionObject();
			$qObj->qTopic = QUESTION_TOPIC_BITMASK;
			$qObj->qType = QUESTION_TYPE_NUMBER_ON;
			$qObj->qParams = array("value" => $val);
			$qObj->aType = ANSWER_TYPE_FILL_BLANKS;
			$qObj->aAmt = ANSWER_AMT_ONE;
			$qObj->ordered = false;
			$qObj->allowNoAnswer = false;
			$qObj->graphState = array("vl" => array(), "el" => array()); //empty graph
		
			return $qObj;
		}
		
		public function checkAnswerNumberOn($qObj, $userAns) {
			$val = $qObj->qParams["value"];
			$ans = gmp_popcount($val);
			return ($userAns[0] == $ans);
		}
		
		//LS One
		public function generateQuestionLSOne() {
			$val = mt_rand(0,63);
			
			$qObj = new QuestionObject();
			$qObj->qTopic = QUESTION_TOPIC_BITMASK;
			$qObj->qType = QUESTION_TYPE_LSONE;
			$qObj->qParams = array("value" => $val);
			$qObj->aType = ANSWER_TYPE_FILL_BLANKS;
			$qObj->aAmt = ANSWER_AMT_ONE;
			$qObj->ordered = false;
			$qObj->allowNoAnswer = false;
			$qObj->graphState = array("vl" => array(), "el" => array()); //empty graph
		
			return $qObj;
		}
		
		public function checkAnswerLSOne($qObj, $userAns) {
			$val = $qObj->qParams["value"];
			$j = (~ $val) + 1;
			
			$ans = intval(log($val & $j, 2));
			return ($userAns[0] == $ans);
		}
	}
?>