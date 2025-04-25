<?php

namespace App\Services;

use App\Entity\Utilisateur;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    /**
     * @param Utilisateur[] $utilisateurs
     * @return string
     * @throws \Exception
     */
    public function utilisateurs(array $utilisateurs): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Utilisateurs de l'application");

        // Tailles des colonnes
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->freezePane('A2');

        // En-têtes
        $sheet
            ->setCellValue('A1', 'Nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Login')
            ->setCellValue('D1', 'Email')
            ->setCellValue('E1', 'Rôle')
            ->setCellValue('F1', 'Actif')
        ;

        // Lignes du tableau
        $m = 2;
        foreach ($utilisateurs as $utilisateur) {
            $sheet
                ->setCellValueByColumnAndRow(1, $m, $utilisateur->getNom())
                ->setCellValueByColumnAndRow(2, $m, $utilisateur->getPrenom())
                ->setCellValueByColumnAndRow(3, $m, $utilisateur->getLogin())
                ->setCellValueByColumnAndRow(4, $m, $utilisateur->getEmail())
                ->setCellValueByColumnAndRow(5, $m, $utilisateur->getRole())
                ->setCellValueByColumnAndRow(6, $m, $utilisateur->getActif() ? 'Oui' : 'Non')
            ;

            $m++;
        }

        // Bordures sur les cellules
        for ($i = 1 ; $i <= 6 ; $i++) {
            $sheet->getStyleByColumnAndRow($i, 1)->getFont()->setBold(true);

            for ($j = 1; $j < $m; $j++) {
                $sheet->getStyleByColumnAndRow($i, $j)->applyFromArray(['borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]]]);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}