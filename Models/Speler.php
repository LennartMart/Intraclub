<?php
    ('_JEXEC') or die;
    /**
     * User: Lennart
     * Date: 1/08/12
     * Time: 14:09
     */

    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/../Interfaces/ISpeler.php');
    require_once(__DIR__ . '/Seizoen.php');
    require_once(__DIR__ . '/Wedstrijd.php');
    require_once(__DIR__ . '/Spelers.php');

    class Speler implements ISpeler
    {
        public $id;
        public $voornaam;
        public $naam;
        public $geslacht;
        public $jeugd;
        public $klassement;
        public $is_lid;
        public $is_veteraan;

        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }

        public function get($speler_id)
        {
            $query = sprintf("SELECT * FROM intra_spelers WHERE id= '%s';", mysql_real_escape_string($speler_id));
            $resultaat = mysql_query($query);
            if($resultaat != FALSE)
            {
                $this->vulop(mysql_fetch_assoc($resultaat));
            }
        }
        public function getRankingHistory($seizoen_id){
            $query = sprintf("SELECT basispunten AS punten
                        FROM intra_spelerperseizoen
                        WHERE speler_id = '%s' AND seizoen_id = '%s'
                      UNION
                      (Select gemiddelde AS punten
                      FROM intra_spelerperspeeldag
                      WHERE speler_id = '%s' ORDER BY speeldag_id)",$this->id,$seizoen_id,$this->id);

            $resultaat = mysql_query($query);
            $punten = array();
            if($resultaat != FALSE)
            {
                while ($punten_array = mysql_fetch_array($resultaat)) {
                    $punten[] = round($punten_array["punten"],2);
                }
            }
            return $punten;

        }
        //Maak een nieuwe speler aan
        public function create($data)
        {

            //Bestaat seizoen al of niet?
            $query = sprintf("SELECT count(id) AS aantal FROM intra_spelers WHERE naam ='%s' AND voornaam = '%s'",$data['naam'], $data['voornaam']);
            $resultaat = mysql_query($query);
            $aantal = @mysql_result($resultaat, 0, aantal);
            if (!$aantal) {
                //Insert een nieuwe rij in de spelerstabel
                //Nieuwe speler is automatisch lid - waarom zou je hem anders toevoegen?
                $query = sprintf("
                INSERT INTO
                    intra_spelers
                SET
                    voornaam = '%s',
                    naam = '%s',
                    geslacht = '%s',
                    jeugd = '%s',
                    klassement= '%s',
                    is_veteraan = '%s',
                    is_lid = 1
                    ",
                    mysql_real_escape_string($data['voornaam']),
                    mysql_real_escape_string($data['naam']),
                    mysql_real_escape_string($data['geslacht']),
                    mysql_real_escape_string($data['jeugd']),
                    mysql_real_escape_string($data['klassement']),
                    mysql_real_escape_string($data['is_veteraan']));

                $result = mysql_query($query);
                if (!$result) {
                    return FALSE;
                } else {
                    //Haal de gegenereerde ID op.
                    $speler_id = mysql_insert_id();

                    $huidig_seizoen = new Seizoen();
                    $huidig_seizoen->get_huidig_seizoen();

                    //Insert een rij voor de speler in het huidige seizoen
                    $query = sprintf("
                                INSERT INTO
                                    intra_spelerperseizoen
                                SET
                                  speler_id = '%d',
                                  seizoen_id = '%d',
                                  basispunten = '%s',
                                  gespeelde_sets = 0,
                                  gewonnen_sets = 0,
                                  gespeelde_punten = 0,
                                  gewonnen_punten = 0
                                  ",
                        mysql_real_escape_string($speler_id),
                        mysql_real_escape_string($huidig_seizoen->id),
                        mysql_real_escape_string($data["basispunten"]));

                    $result = mysql_query($query);
                    if (!$result) {
                        return FALSE;
                    }
                    return TRUE;
                }
            }
            else
            {
                return FALSE;
            }

        }






    }
