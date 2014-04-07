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
                    naam = '%s',
                    geslacht = '%s',
                    jeugd = '%s',
                    klassement= '%s',
                    is_lid = '%s'

                WHERE
                    id = '%s';
                ",
                    mysql_real_escape_string($data['voornaam']),
                    mysql_real_escape_string($data['naam']),
                    mysql_real_escape_string($data['geslacht']),
                    mysql_real_escape_string($data['jeugd']),
                    mysql_real_escape_string($data['klassement']),
                    mysql_real_escape_string($data['is_lid']),
                    mysql_real_escape_string($data['id']));
            return mysql_query($query);
        }

        public function update_seizoenstats($data)
        {

            $query = sprintf("
            UPDATE
                intra_spelerperseizoen
            SET
                gespeelde_sets = '%s',
                gewonnen_sets = '%s',
                gespeelde_punten= '%s',
                gewonnen_punten = '%s'

            WHERE
                speler_id = '%s' and seizoen_id = '%s';
            ",
                mysql_real_escape_string($data['gespeelde_sets']),
                mysql_real_escape_string($data['gewonnen_sets']),
                mysql_real_escape_string($data['gespeelde_punten']),
                mysql_real_escape_string($data['gewonnen_punten']),
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($data['seizoen']));

            echo "$query <br/>";
            return mysql_query($query);
        }

        //TODO: Wat als er nog geen data zit in db?
        //FIx? ON DUPLICATE KEY
        public function update_speeldagstats( $speeldag_id, $tussenstand_speeldag)
        {
            if($this->id == null || $speeldag_id == null) return FALSE;
            $query = sprintf("
            INSERT INTO
                intra_spelerperspeeldag
            SET
                gemiddelde = '%s',
                speler_id = '%s',
                speeldag_id = '%s'
            ON DUPLICATE KEY UPDATE
                gemiddelde = '%s'
            ",
            mysql_real_escape_string($tussenstand_speeldag),
            mysql_real_escape_string($this->id),
            mysql_real_escape_string($speeldag_id),
            mysql_real_escape_string($tussenstand_speeldag));

            return mysql_query($query);
        }

        public function vulop($speler)
        {
            $this->id = $speler['id'];
            $this->voornaam = $speler['voornaam'];
            $this->naam = $speler['naam'];
            $this->geslacht = $speler['geslacht'];
            $this->jeugd = $speler['jeugd'];
            $this->klassement = $speler['klassement'];
            $this->is_lid = $speler['is_lid'];
        }

        /**
         * @param $seizoen_id
         * @return Wedstrijd[]
         */
        public function get_wedstrijden($seizoen_id=null)
        {
            if($seizoen_id==null)
            {
                $seizoen = new Seizoen();
                $seizoen->get_huidig_seizoen();
                $seizoen_id = $seizoen->id;
            }
            $query = sprintf("SELECT * FROM  intra_wedstrijden iw
                                  INNER JOIN intra_speeldagen ispeel ON iw.speeldag_id = ispeel.id

                                  WHERE (
                                          (
                                             iw.team1_speler1='%s' OR
                                             iw.team1_speler2='%s' OR
                                             iw.team2_speler1='%s' OR
                                             iw.team2_speler2='%s'
                                          ) AND ispeel.seizoen_id = '%s'
                                        )
                                  ORDER BY iw.id ASC;",
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
