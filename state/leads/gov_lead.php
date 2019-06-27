<?php

// For Governor basic info
$govStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=1");
$govStmt->execute();
$govInfo = $govStmt->fetch(PDO::FETCH_ASSOC);

// For Lieutenant Governor basic info
$ltgovStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=2");
$ltgovStmt->execute();
$ltgovInfo = $ltgovStmt->fetch(PDO::FETCH_ASSOC);

// For Attorney General basic info
$attGenStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=3");
$attGenStmt->execute();
$attGenInfo = $attGenStmt->fetch(PDO::FETCH_ASSOC);

// For State Treasurer basic info
$treasStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=4");
$treasStmt->execute();
$treasInfo = $treasStmt->fetch(PDO::FETCH_ASSOC);

// For State Auditor basic info
$auditStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=5");
$auditStmt->execute();
$auditInfo = $auditStmt->fetch(PDO::FETCH_ASSOC);

// For Secretary of State basic info
$secStmt = $pdo->prepare(
  "SELECT * FROM Delegate INNER JOIN Job WHERE Delegate.delegate_id=Job.delegate_id AND Job.job_id=6");
$secStmt->execute();
$secInfo = $secStmt->fetch(PDO::FETCH_ASSOC);

// print_r($govInfo);

?>
