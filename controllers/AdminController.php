<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController  {


    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Affichage de la page de Monitoring
     * @return void
     */
    public function showMonitoring() : void
    {
        $sort = Utils::request('sort', 'asc');
        $column = Utils::request('column', 'id');
        $this->checkIfUserIsConnected();


        $articleManager = new ArticleManager();
        $articles = $articleManager->findAllForMonitoring($column, $sort);
        $commentManager = new CommentManager();

        foreach ($articles as $article) {
            $article->setComments($commentManager->getCommentCount($article->getId()));
        }

        $view = new View("Détail des articles");
        $view->render("monitoring", ['articles' => $articles]);
    }


    /**
     * Suppression d'un commentaire
     * @return void
     */
    public function deleteComment() : void
    {
        $articleId = Utils::request('id', 0);
        $commentId = Utils::request('commentId', 0);
        
        if ($articleId <= 0 || $commentId <= 0) {
            echo "ID d'article ou de commentaire invalide.";
            return;
        }
    
        $commentManager = new CommentManager();
        $commentToDelete = $commentManager->getCommentById($commentId);
    
        if ($commentToDelete) {
            if ($commentManager->deleteComment($commentToDelete)) {
                header("Location: ?action=showComments&id=$articleId");
                exit;
            } else {
                echo "Échec de la suppression du commentaire.";
            }
        } else {
            echo "Le commentaire avec l'ID spécifié n'a pas été trouvé.";
        }
    }

    /**
     * Affichage de la page de gestion des Commentaires
     * @return void
     */
    public function showComments() : void
    {
        $articleId = Utils::request('id', 0);
        $this->checkIfUserIsConnected();

        if ($articleId <= 0) {
            echo "ID d'article invalide.";
            return;
        }

        $commentManager = new CommentManager();
        
        $commentToDelete = Utils::request('deleteComment', null);
        if (isset($commentToDelete)) {
            $this->deleteComment();
            return; // La redirection se fera dans la méthode deleteComment
        }
    
        
        $comments = $commentManager->getAllCommentsByArticleId($articleId);

        $view = new View("Détail des commentaires");
        $view->render("comments", ['comments' => $comments]);
    }
}