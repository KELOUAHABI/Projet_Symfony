Projet e-application : création d'un blog avec Symfony 3
========================

Blog réalisé avec Symfony 3, en utilisant les bundles :

  * [**FOSUserBundle**] pour la gestion des utilisateur et la securité des routes.

  * [**EasyAdminBundle**] pour la gestion des entités.

  * [**IvoryCKEditorBundle**] pour l'affichage d'un textarea WYSIWYG.

-----------------------------

La gestion des produits et des articles se fait de deux façon :

# 1ère methode:

## Produits :

  * Ajout : `/product/add`
  * Modification : `/product/edit/{id}`

## Articles :

  * Ajout : `/article/add`
  * Modification : `/article/edit/{id}`

les liens sont securisés, et ne sont accéssible qu'à un utilisateur avec le role super_admin.

# 2ème methode:

 * à partir du backoffice, accéssible depuis la route `/admin`, accéssible au role super_admin


 ```yml
 #  app/config/security.yml
 access_control:
     - { path: ^/admin/, role: ROLE_ADMIN }
     - { path: ^/article/edit/, role: ROLE_ADMIN }
     - { path: ^/article/add, role: ROLE_ADMIN }
     - { path: ^/product/edit/, role: ROLE_ADMIN }
     - { path: ^/product/add, role: ROLE_ADMIN }

 ```

 -----------------------------

 Un Utilisateur super_admin est codé en brut :

 ```yml
  #  app/config/security.yml
 in_memory:
     memory:
         users:
             super_admin:
                 password: super_pass
                 roles: 'ROLE_ADMIN'
 ```
 -----------------------------
