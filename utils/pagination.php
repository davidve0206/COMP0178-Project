<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
    <ul class="pagination justify-content-center">

        <?php

        // Copy any currently-set GET variables to the URL.
        $querystring = "";
        foreach ($_GET as $key => $value) {
            if ($key != "page") {
                $querystring .= "$key=$value&amp;";
            }
        }

        if ($num_results > 0) {

            $high_page_boost = max(3 - $curr_page, 0);
            $low_page_boost = max(2 - ($max_page - $curr_page), 0);
            $low_page = max(1, $curr_page - 2 - $low_page_boost);
            $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

            if ($curr_page != 1) {
                echo ('
      <li class="page-item">
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
          <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
          <span class="sr-only">Previous</span>
        </a>
      </li>');
            }

            for ($i = $low_page; $i <= $high_page; $i++) {
                if ($i == $curr_page) {
                    // Highlight the link
                    echo ('
      <li class="page-item active">');
                } else {
                    // Non-highlighted link
                    echo ('
      <li class="page-item">');
                }

                // Do this in any case
                echo ('
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
      </li>');
            }

            if ($curr_page != $max_page) {
                echo ('
      <li class="page-item">
        <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
          <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
          <span class="sr-only">Next</span>
        </a>
      </li>');
            }
        }

        ?>

    </ul>
</nav>