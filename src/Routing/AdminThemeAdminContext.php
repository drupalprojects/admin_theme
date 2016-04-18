<?php

namespace Drupal\admin_theme\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\Routing\Route;

/**
 * Decorates the core AdminContext to check custom admin paths.
 *
 * @todo Do not extend the decorated class, implement an interface.
 */
class AdminThemeAdminContext extends AdminContext {

  /**
   * The decorated admin context service.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $subject;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * AdminThemeAdminContext constructor.
   *
   * @param \Drupal\Core\Routing\AdminContext $subject
   *   The decorated admin context service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(AdminContext $subject, RouteMatchInterface $route_match, ConfigFactoryInterface $config_factory) {
    $this->subject = $subject;
    $this->routeMatch = $route_match;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function isAdminRoute(Route $route = NULL) {
    if (!$route) {
      $route = $this->routeMatch->getRouteObject();
      if (!$route) {
        return FALSE;
      }
    }

    $paths = $this->configFactory->get('admin_theme.settings')->get('paths');
    $paths = array_map('trim', explode("\n", $paths));

    $internal_path = '/' . Url::fromRouteMatch($this->routeMatch)->getInternalPath();
    if (in_array($internal_path, $paths, TRUE)) {
      return TRUE;
    }

    return $this->subject->isAdminRoute($route);
  }

}
