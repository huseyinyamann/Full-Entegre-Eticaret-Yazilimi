<?php /** @var array $post @var int $i */ ?>
<article class="post-card">
    <a href="<?= url('blog/' . $post['slug']) ?>" class="post-card__img">
        <img src="<?= img($post['image'] ?? null, 800, 520, 'post' . $i) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
        <span class="post-card__date"><?= e(date('d.m.Y', strtotime($post['date']))) ?></span>
    </a>
    <h3><a href="<?= url('blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3>
    <p><?= e($post['excerpt']) ?></p>
    <a href="<?= url('blog/' . $post['slug']) ?>" class="link-more">Devamını Oku <i class="bi bi-arrow-right"></i></a>
</article>
