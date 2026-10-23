<?php
session_start();

$action = $_GET['action'] ?? 'liste';

/*
match ($action) {
  'liste'      => (new AnnonceController())->liste(),
  'creer'      => (new AnnonceController())->creer(),
  'postuler'   => (new CandidatureController())->postuler(),
  'noter'      => (new AvisController())->noter(),
  default        => (new AnnonceController())->liste(),
};
*/