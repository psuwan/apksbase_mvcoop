<?php
namespace App\Core;

class Controller
{
    /**
     * Render a view with optional layout.
     * If a layout file exists (Views/layout.phtml by default), the inner view's
     * output is captured to $content and injected into the layout.
     * You can pass 'layout' => 'layout' (default) to choose a specific layout
     * or 'layout' => null/false to disable wrapping.
     *
     * @param string $view Path under app/Views without extension
     * @param array $data Associative array of data for the view/layout
     * @return string
     */
    protected function view(string $view, array $data = array()): string
    {
        $viewsDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
        // Support both .php and .phtml view extensions
        $viewFilePhp = $viewsDir . $view . '.php';
        $viewFilePhtml = $viewsDir . $view . '.phtml';
        $viewFile = file_exists($viewFilePhp) ? $viewFilePhp : (file_exists($viewFilePhtml) ? $viewFilePhtml : null);
        if ($viewFile === null) {
            return 'View not found: ' . htmlspecialchars($view);
        }

        // Extract data for inner view
        extract($data, EXTR_SKIP);
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Determine layout
        $layoutName = $data['layout'] ?? 'layout';
        if ($layoutName === null || $layoutName === false || $layoutName === '') {
            return $content;
        }
        $layoutFile = $viewsDir . $layoutName . '.phtml';
        if (!file_exists($layoutFile)) {
            // If layout not found, return content alone to avoid breaking pages
            return $content;
        }

        // Make variables available to layout too
        // $content already contains the inner view output
        // Keep $data available via $this->data if needed in future
        // For simplicity, re-extract to provide $title, etc., to layout
        extract($data, EXTR_SKIP);
        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }
}
