<?php

namespace Drupal\madrassa_parent\Entity;

interface MadrassaParentInterface
{
  public function getCivility(): string;
  public function getLinkFullName();
  public function getFullName(): string;
  public function getPhone(): string;
  public function getEmail(): string;
  public function getAddress(): string;
  public function getFonction(): string;
  public function getTypeUser(): string;
  public function getPath();
}