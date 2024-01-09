<?php 
    /** 
     * Affichage de la partie de gestion des commentaires : liste des commentaires avec un bouton "supprimer" pour chacun. 
     */
?>

<div class="comments">
    <h2 class="commentsTitle">Gestion des commentaires</h2>
    <?php 
        if (empty($comments)) {
            echo '<p class="info">Aucun commentaire pour cet article.</p>';
        } else {
            echo '<ul>';
            $articleId = isset($_GET['id']) ? $_GET['id'] : 0;

            foreach ($comments as $comment) {
                echo '<li>';
                echo '  <div class="smiley">☻</div>';
                echo '  <div class="detailComment">';
                echo '      <h3 class="info">Le ' . Utils::convertDateToFrenchFormat($comment->getDateCreation()) . ", " . Utils::format($comment->getPseudo()) . ' a écrit :</h3>';
                echo '      <p class="content">' . Utils::format($comment->getContent()) . '</p>';
                echo '      <a class="supprimer" href="?action=showComments&id=' . $articleId . '&deleteComment&commentId=' . $comment->getId() . '">Supprimer</a>';
                echo '  </div>';
                echo '</li>';
            }               
            echo '</ul>';
        } 
    ?>
</div>