<?php

namespace Controllers;

class Article extends Controller {

    protected $modelName = \Models\Article::class;
    
    /**
     * Affiche tous les articles
     *
     * @return void
     */
    public function index()
    {
        $model = new \Models\Article();
        /**
         * Récupération des articles
         */
        $articles = $model->findAll("created_at DESC");
        /**
         * Affichage
         */
        $pageTitle = "Accueil";
        \Renderer::render('articles/index', compact('pageTitle', 'articles'));
    }

    /**
     * Affiche UN article
     *
     * @return void
     */
    public function show()
    {      
        $modelArticle = new \Models\Article();
        $modelComment = new \Models\Comment();
        /**
         * 1. Récupération du param "id" et vérification de celui-ci
         */
        // On part du principe qu'on ne possède pas de param "id"
        $article_id = null;
        // Mais si il y'en a un et que c'est un nombre entier, alors c'est cool
        if (!empty($_GET['id']) && ctype_digit($_GET['id'])) {
            $article_id = $_GET['id'];
        }
        // On peut désormais décider : erreur ou pas ?!
        if (!$article_id) {
            die("Vous devez préciser un paramètre `id` dans l'URL !");
        }
        /**
         * 3. Récupération de l'article en question
         * On va ici utiliser une requête préparée car elle inclue une variable qui provient de l'utilisateur : Ne faites
         * jamais confiance à ce connard d'utilisateur ! :D
         */
        $article = $modelArticle->find($article_id);
        /**
         * 4. Récupération des commentaires de l'article en question
         * Pareil, toujours une requête préparée pour sécuriser la donnée filée par l'utilisateur (cet enfoiré en puissance !)
         */
        $commentaires = $modelComment->findAllWithArticle($article_id);
        /**
         * 5. On affiche 
         */
        $pageTitle = $article['title'];
        \Renderer::render('articles/show', compact('pageTitle', 'article', 'commentaires', 'article_id'));
    }
    
    /**
     * Supprime un article
     *
     * @return void
     */
    public function delete()
    {
        /**
         * 1. On vérifie que le GET possède bien un paramètre "id" (delete.php?id=202) et que c'est bien un nombre
         */
        if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
            die("Ho ?! Tu n'as pas précisé l'id de l'article !");
        }
        
        $id = $_GET['id'];
        
        /**
         * 2. Vérification que l'article existe bel et bien
         */
        $model = new \Models\Article();
        $article = $model->find($id);
        
        if (!$article) {
            die("L'article $id n'existe pas, vous ne pouvez donc pas le supprimer !");
        }
        
        /**
         * 3. Réelle suppression de l'article
         */
        $model->delete($id);
        
        /**
         * 4. Redirection vers la page d'accueil
         */
        \Http::redirect('index.php');
    }
}