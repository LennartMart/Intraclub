<?php
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
        public $achternaam;
        public $geslacht;
        public $jeugd;
        public $klassement;
        public $is_lid;

        function __construct()
        {
            $this->db = new ConnectionSettings();
            $this->db->connect();
        }

        //Maak een nieuwe speler aan
        public function create($data)
        {
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
                is_lid = 1
                ",
                mysql_real_escape_string($data['voornaam']),
                mysql_real_escape_string($data['naam']),
                mysql_real_escape_string($data['geslacht']),
                mysql_real_escape_string($data['jeugd']),
                mysql_real_escape_string($data['klassement']));

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

                echo $query;

                $result = mysql_query($query);
                if (!$result) {
                    return FALSE;
                }
                return TRUE;
            }

        }

        public function get_seizoen_stats($seizoen_id)
        {
            $query = sprintf("SELECT * from intra_spelerperseizoen where speler_id = '%s' AND seizoen_id = '%s';",
                                mysql_real_escape_string($this->id),
                                mysql_real_escape_string($seizoen_id)
                            );
            $resultaat = mysql_query($query);

            //return assoc tabel
            return mysql_fetch_assoc($resultaat);
        }

        public function get_speeldagstats($speeldag_id)
        {
            $query = sprintf("SELECT * from intra_spelerperspeeldag where speler_id = '%s' AND speeldag_id = '%s';",
                                mysql_real_escape_string($this->id),
                                mysql_real_escape_string($speeldag_id)
                            );

            $resultaat = mysql_query($query);
            //return assoc tabel
            return mysql_fetch_assoc($resultaat);
        }

        public function update_basisinfo($data)
        {
            $query = sprintf("
                UPDATE
                    intra_spelers
                SET
                    voornaam = '%s',
                    achternaam = '%s',
                    geslacht = '%s',
                    jeugd = '%s',
                    klassement_id= '%s'
                    is_lid = '%s'

                WHERE
                    id = '%s';
                ",
                    mysql_real_escape_string($data['voornaam']),
                    mysql_real_escape_string($data['achternaam']),
                    mysql_real_escape_string($data['geslacht']),
                    mysql_real_escape_string($data['jeugd']),
                    mysql_real_escape_string($data['klassement']),
                    mysql_real_escape_string($data['is_lid']),
                    mysql_real_escape_string($data['id']));

            $gelukt = mysql_query($query);
            if ($gelukt) {
                $huidig_seizoen = Seizoen::huidig_seizoen();
                $query = sprintf("
                            UPDATE
                                intra_spelerperseizoen
                            SET
                                is_lid = '%s',

                            WHERE
                                speler_id = '%s'
                                AND
                                seizoen_id = '%s';
                            ",
                            mysql_real_escape_string($data['is_lid']),
                            mysql_real_escape_string($data['id']),
                            mysql_real_escape_string($huidig_seizoen->id));
                return mysql_query($query);
            } else {
                return false;
            }
        }


        public function update_seizoenstats($data)
        {

            $query = sprintf("
            UPDATE
                intra_spelerperseizoen
            SET
                basispunten = '%s',
                huidige_punten = '%s',
                gespeelde_sets = '%s',
                gewonnen_sets = '%s',
                gespeelde_punten= '%s'
                gewonnen_punten = '%s'

            WHERE
                speler_id = '%s' and seizoen_id = '%s';
            ",
                mysql_real_escape_string($data['basispunten']),
                mysql_real_escape_string($data['huidige_punten']),
                mysql_real_escape_string($data['gespeelde_sets']),
                mysql_real_escape_string($data['gewonnen_sets']),
                mysql_real_escape_string($data['gespeelde_punten']),
                mysql_real_escape_string($data['gewonnen_punten']),
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($data['seizoen']));

            return mysql_query($query);
        }

        //TODO: Wat als er nog geen data zit in db?
        //FIx? ON DUPLICATE KEY
        public function update_speeldagstats($speler_id, $speeldag_id, $tussenstand_speeldag, $ranking)
        {
            $query = sprintf("
            INSERT INTO
                intra_spelerperspeeldag
            SET
                ranking = '%s',
                gemiddelde = '%s',
                speler_id = '%s',
                speeldag_id = '%s'
            ON DUPLICATE KEY UPDATE
                ranking = '%s',
                gemiddelde = '%s'
            ",
            mysql_real_escape_string($ranking),
            mysql_real_escape_string($tussenstand_speeldag),
            mysql_real_escape_string($speler_id),
            mysql_real_escape_string($speeldag_id),
            mysql_real_escape_string($ranking),
            mysql_real_escape_string($tussenstand_speeldag));

            return mysql_query($query);
        }

        public function vulop($speler)
        {
            $this->id = $speler['id'];
            $this->voornaam = $speler['voornaam'];
            $this->achternaam = $speler['achternaam'];
            $this->geslacht = $speler['geslacht'];
            $this->jeugd = $speler['jeugd'];
            $this->klassement = $speler['klassement'];
        }

        /**
         * @param $seizoen_id
         * @return Wedstrijd[]
         */
        public function get_wedstrijden($seizoen_id)
        {
            $query = sprintf("SELECT * FROM  intra_wedstrijden
                                  WHERE (
                                          (
                                             team1_speler1='%s' OR
                                             team1_speler2='%s' OR
                                             team2_speler1='%s' OR
                                             team2_speler2='%s'
                                          ) AND seizoen_id = '%s'
                                        )
                                  ORDER BY id ASC;",
                            mysql_real_escape_string($this->id),
                            mysql_real_escape_string($this->id),
                            mysql_real_escape_string($this->id),
                            mysql_real_escape_string($this->id),
                            mysql_real_escape_string($seizoen_id));

            $resultaat = mysql_query($query);

            $wedstrijden = array();

            while ($wedstrijd_array = mysql_fetch_array($resultaat)) {
                $wedstrijd = new Wedstrijd();
                $wedstrijd->vulop($wedstrijd_array);
                $wedstrijden[] = $wedstrijd;
            }

            return $wedstrijden;
        }
    }
