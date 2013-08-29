<?php
/**
 * User: Lennart
 * Date: 16-5-13
 * Time: 22:03
 */
    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/Spelers.php');
    require_once(__DIR__ . '/Wedstrijd.php');
    require_once(__DIR__ . '/Speler.php');
    require_once(__DIR__ . '/Speeldag.php');
    require_once(__DIR__ . '/Seizoen.php');
class Ranking {

    function getAlgemeneRanking($seizoen_id = null, $speeldag_id = null)
    {
        $this->checkSpeeldagenSeizoen($seizoen_id,$speeldag_id);

        $query = sprintf("SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd, ISPS.ranking AS ranking, ISPS.gemiddelde AS gemiddelde
                                  FROM  intra_spelerperspeeldag ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  WHERE (
                                          ISPS.speeldag_id = '%s'
                                        )
                                  ORDER BY gemiddelde DESC;",
            mysql_real_escape_string($speeldag_id));

        $resultaat = mysql_query($query);

        $ranking = array();

        while ($ranking_array = mysql_fetch_array($resultaat)) {

            $ranking[] = $ranking_array;
        }

        return $ranking;
    }

    function getVrouwenRanking($seizoen_id, $speeldag_id = null)
    {
        $this->checkSpeeldagenSeizoen($seizoen_id,$speeldag_id);

        $query = sprintf("SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd, ISPS.ranking AS ranking, ISPS.gemiddelde AS gemiddelde
                                  FROM  intra_spelerperspeeldag ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  WHERE (
                                          ISPS.speeldag_id = '%s' AND ISP.geslacht = 'Vrouw'
                                        )
                                 ORDER BY gemiddelde DESC;",
            mysql_real_escape_string($speeldag_id));

        $resultaat = mysql_query($query);

        $ranking = array();

        while ($ranking_array = mysql_fetch_array($resultaat)) {

            $ranking[] = $ranking_array;
        }

        return $ranking;
    }

    function getJeugdRanking($seizoen_id, $speeldag_id = null)
    {
        $this->checkSpeeldagenSeizoen($seizoen_id,$speeldag_id);

        $query = sprintf("SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd, ISPS.ranking AS ranking, ISPS.gemiddelde AS gemiddelde
                                  FROM  intra_spelerperspeeldag ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  WHERE (
                                          ISPS.speeldag_id = '%s' AND ISP.jeugd =  1
                                        )
                                  ORDER BY gemiddelde DESC;",
            mysql_real_escape_string($speeldag_id));

        $resultaat = mysql_query($query);

        $ranking = array();

        while ($ranking_array = mysql_fetch_array($resultaat)) {

            $ranking[] = $ranking_array;
        }

        return $ranking;
    }

    private function checkSpeeldagenSeizoen(&$seizoen_id, &$speeldag_id)
    {
        if($seizoen_id == null)
        {
            $seizoen = new Seizoen();
            $seizoen->get_huidig_seizoen();
            $seizoen_id = $seizoen->id;
        }
        if($speeldag_id == null)
        {
            $laatste_speeldag = new Speeldag();
            $laatste_speeldag->get_laatste_speeldag($seizoen_id);
            $speeldag_id = $laatste_speeldag->id;
        }
    }

}