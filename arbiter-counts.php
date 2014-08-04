<?php if (!isset($_FILES['arbiter'])) { ?>
<form enctype="multipart/form-data" method="post">
   <input type="file" name="arbiter" />
   <input type="text" name="name" value="Sam Craig" />
   <input type="submit" />
</form>
format from arbiter | print schedule | excel worksheet format (.xls) - Schedule.xls, then resave as CSV
<?php 
}
else {
   // zero-based index
   $COL_NAME = 4;
   $COL_GAMETYPE = 6;
   $COL_OFFICIAL = 1;

   $name = $_POST['name'];
   $lines = file($_FILES['arbiter']['tmp_name']);
   
   // set vars
   $gametype = '';
   $games = array();
   
   foreach ($lines as $idx => $line) {
      if ($idx < 10) continue; // skip the first 10 lines
      
      $cols = explode(',',$line); // get the line as an array

      if ($cols[0] == 'Game') continue; // this is a header row
      
      if (!empty($cols[$COL_GAMETYPE])) { // concatenate the "game type" as <org> - <level> (e.g. HYSA - Boys U/19)
         $gametype .= (empty($gametype) ? $cols[$COL_GAMETYPE] : ' - ' . $cols[$COL_GAMETYPE]);
      }
      else {
         if ($cols[$COL_OFFICIAL] == 'AR1' || $cols[$COL_OFFICIAL] == 'AR2' || $cols[$COL_OFFICIAL] == 'AR') $cols[$COL_OFFICIAL] = 'AR';
         if ($cols[$COL_OFFICIAL] == 'Ref' || $cols[$COL_OFFICIAL] == 'Referee') $cols[$COL_OFFICIAL] = 'Referee';
         if ($cols[$COL_NAME] == $name) {
            $games[$gametype][$cols[$COL_OFFICIAL]] = isset($games[$gametype][$cols[$COL_OFFICIAL]]) ? $games[$gametype][$cols[$COL_OFFICIAL]]+1 : 1;
            $gametype = '';
         }
      }
   }
   
   $assignments = array();
   foreach($games as $level => $game) {
      foreach($game as $assignment => $count)
         $assignments[$assignment][$level] = isset($assignments[$assignment][$level]) ? $assignments[$assignment][$level]+$count : $count;//$count;
   }
   
   echo '<pre>';
   //foreach($games as $level => $game) {
   //   echo $level . '<br/>';
   //   foreach($game as $assignment => $count)
   //      echo '     ' . $count . '  ' . $assignment . '<br/>';
   //}
   
   //echo '<br/><br/>';
   
   foreach($assignments as $assignment => $level) {
      $total = 0;
      foreach($level as $game => $count)
         $total += $count;
      echo $assignment . '   (' . $total . ')<br/>';
      foreach($level as $game => $count)
         echo '     ' . $count . '  ' . $game . '<br/>';
   }
   
   echo '</pre>';
   //Game			Date & Time			Sport & Level		Site				Home					Away	
   //
   //
   //9845			10/14/2012			HYSA		Waipio Field #16				1804 Honolulu Galaxy 					1808 TOA	
   //			(Sun) 8:30 AM			Boys U/19						FC 95B						
   //	Referee			Derek Wong			8		C: 808-780-6370									Accepted
   //	AR			Sam Craig			23											Accepted
   //	AR			Dave Felton			8		C: 808-285-6252				H: 808-744-5249					Accepted

   
   
}
?>