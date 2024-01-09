<?php 
    /** 
     * Affichage de la partie des détails des articles (avec leur nombre de vues et autres détails important) 
     */
?>

<h2>Détail des articles</h2>

<div class="adminMonitoring">
    <table>
        <thead>
            <tr>
                <th>
                    <div class="arrayHeader">
                        <p>Titre</p>
                        <div class="arrayArrow">
                            <a class="arrow" href="?action=showMonitoring&column=title&sort=asc"></a>
                            <a class="arrow-down" href="?action=showMonitoring&column=title&sort=desc"></a>
                        </div>
                    </div>    
                </th>
                <th>
                    <div class="arrayHeader">
                        <p>Nombre de vues</p>
                        <div class="arrayArrow">
                            <a class="arrow" href="?action=showMonitoring&column=views&sort=asc"></a>
                            <a class="arrow-down" href="?action=showMonitoring&column=views&sort=desc"></a>
                        </div>
                    </div>
                </th>
                <th>
                    <div class="arrayHeader">
                        <p>Nombre de commentaires</p>
                        <div class="arrayArrow">
                            <a class="arrow" href="?action=showMonitoring&column=totalComment&sort=asc"></a>
                            <a class="arrow-down" href="?action=showMonitoring&column=totalComment&sort=desc"></a>
                        </div>
                    </div>
                </th>
                <th>
                    <div class="arrayHeader">
                        <p>Date de création</p>
                        <div class="arrayArrow">
                            <a class="arrow" href="?action=showMonitoring&column=date_creation&sort=asc"></a>
                            <a class="arrow-down" href="?action=showMonitoring&column=date_creation&sort=desc"></a>
                        </div>
                    </div>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article) { ?>
                <tr>
                    <td><?= $article->getTitle(); ?></td>
                    <td><?= $article->getViews(); ?></td>
                    <td><?= $article->getComments(); ?></td>
                    <td><?= $article->getDateCreation()->format('Y-m-d H:i:s'); ?></td>
                    <td><a href="?action=showComments&id=<?= $article->getId(); ?>">Afficher les commentaires</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

