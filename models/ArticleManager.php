<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager 
{
    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles(): array
    {
        $sql = "SELECT * FROM article";
        $result = $this->db->query($sql);
        $articles = [];
    
        while ($articleData = $result->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($articleData);
            $article->setComments($article->getComments());
            $articles[] = $article;
        }
    
        return $articles;
    }

    public function findAllForMonitoring(string $column, string $sort): array
    {
        // Récupération de tous les articles
        $articles = $this->getAllArticles();

        // Fonction de comparaison pour le tri
        $compareFunction = function ($article1, $article2) use ($column, $sort) {
            switch ($column) {
                case 'title':
                    $value1 = $article1->getTitle();
                    $value2 = $article2->getTitle();
                    break;

                case 'views':
                    $value1 = $article1->getViews();
                    $value2 = $article2->getViews();
                    break;

                case 'totalComment':
                    $value1 = $article1->getComments();
                    $value2 = $article2->getComments();
                    break;

                case 'date_creation': 
                    $value1 = $article1->getDateCreation()->format('Y-m-d H:i:s');;
                    $value2 = $article2->getDateCreation()->format('Y-m-d H:i:s');;
                    break;

                default:
                    // Si la colonne n'est pas reconnue, on utilise le titre par défaut
                    $value1 = $article1->getTitle();
                    $value2 = $article2->getTitle();
            }

            if ($sort === 'asc') {
                return strnatcmp($value1, $value2);
            } else {
                return strnatcmp($value2, $value1);
            }
        };
        
        usort($articles, $compareFunction);

        return $articles;
    }
    
    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id) : ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }

    /**
     * Ajoute ou modifie un article.
     * On sait si l'article est un nouvel article car son id sera -1.
     * @param Article $article : l'article à ajouter ou modifier.
     * @return void
     */
    public function addOrUpdateArticle(Article $article) : void 
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    /**
     * Ajoute un article.
     * @param Article $article : l'article à ajouter.
     * @return void
     */
    public function addArticle(Article $article) : void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation, date_update) VALUES (:id_user, :title, :content, NOW(), NOW())";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    /**
     * Modifie un article.
     * @param Article $article : l'article à modifier.
     * @return void
     */
    public function updateArticle(Article $article) : void
    {
        $sql = "UPDATE article SET title = :title, content = :content, date_update = NOW() WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    /**
     * Supprime un article.
     * @param int $id : l'id de l'article à supprimer.
     * @return void
     */
    public function deleteArticle(int $id) : void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Incrémente le nombre de vues d'un article. 
     * @param int $id : l'id de l'article qui obtient une vue supplémentaire
     * @return void
     */
    public function incrementViews(int $id) : void
    {
        $sql = "UPDATE article SET views = views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Récupère le nombre de vues d'un article
     * @param int $id : l'id de l'article dont l'on veut récupérer les vues
     * @return int le nombre de vues
     */
    public function getViewsCount(int $id) : int
    {
        $sql = "SELECT views FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $viewCount = $result->fetchColumn();

        return $viewCount !== false ? (int) $viewCount : 0;
    }

    public function CreationDate(int $id) : string
    {
        $sql = "SELECT date_creation FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $dateCreation = $result->fetchColumn();

        return $dateCreation !== false ? (string) $dateCreation : "ERROR";
    }
}