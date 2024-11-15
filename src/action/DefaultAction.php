<?php

namespace iutnc\nrv\action;

class DefaultAction extends Action
{
    /**
     * Affiche un message de bienvenue
     * @return string
     */
    public function execute(): string
    {
        // Si un utilisateur est connecté, on affiche son nom
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $message = <<<HTML
                <section class="section">
                <h2 class="title">Bienvenue {$utilisateur->getNom()}  !</h2>
                <p>
                    Vous pouvez utiliser ce site pour gérer la programmation du festival <strong>Nancy Rock Vibrations</strong>.
                </p>
                </section>
            HTML;
        } else {
            // Sinon, on affiche un message de bienvenue par défaut
            $message = <<<HTML
            <section class="section">
                <h1 class="title">Bienvenue sur le site officiel de Nancy Rock Vibrations ! 🎸🔥</h1>
                <p>
                    Plongez au cœur de l'expérience rock ultime dans la belle ville de Nancy ! Que vous soyez un passionné de riffs endiablés, 
                    un amateur de vibes électriques ou simplement curieux de découvrir une ambiance unique, 
                    <strong>Nancy Rock Vibrations</strong> est <em>LE</em> rendez-vous à ne pas manquer.
                </p>
                    <h2 class="subtitle">🎶 Les artistes</h2>
                    <p>Une programmation explosive mêlant talents locaux, stars internationales et pépites émergentes.</p>
                    <h2 class="subtitle">🌟 Infos pratiques</h2>
                    <p>Tout ce que vous devez savoir pour profiter pleinement du festival se trouve sur le site.</p>
                <p class="cta">
                    Préparez-vous à vibrer, chanter, danser et célébrer la musique dans une ambiance festive et conviviale. 
                    <strong>Nancy Rock Vibrations</strong> n'attend plus que vous !
                </p>
                <p class="hashtag">
                    #NancyRockVibrations #LetTheMusicPlay 🎤✨
                </p>
            </section>
        HTML;
        }

        return $message;
    }
}