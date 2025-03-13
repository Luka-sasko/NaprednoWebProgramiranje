<?php
interface iRadovi {
    public function create(string $naziv_rada, string $tekst_rada, string $link_rada, string $oib_tvrtke): void;
    public function save(): void;
    public function read(): array;
}