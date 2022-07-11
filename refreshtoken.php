<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $title; ?></title>
    <meta content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.css" />
</head>

<body>
    <nav class="navbar sticky-top navbar-expand-xl bg-primary">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand" style="color: white" href="index.php"><?php echo $title; ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <?php include(__DIR__ . '/resources/controllers/session.php'); ?>
    <br>
    <main role="main" class="container">
        <div class="jumbotron">
            <img class="sticky" src="<?php echo $img; ?>" alt="Logo" style="height: 60px; width: 60px; margin-bottom: 20px;">
            <h1 style="display: inline;"><?php echo $title; ?></h1>
            <p style="margin-bottom: 0px;"><strong>Client ID: </strong> <?php echo $clientId; ?></p>
            <?php if (!empty($clientSecret)) {
                echo "<p><b>Client Secret: </b> $clientSecret</p>";
            }
            ?>
            <br>

            <?php if ($enableActiveTokensTable) : ?>
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#accessTokenMenu">My Access Token</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#refreshTokenMenu">My Refresh Tokens</a></li>
            </ul>
            <?php endif; ?>

            <div class="tab-content">
                <div id="accessTokenMenu" class="tab-pane active">
                    <div>
                        <p class="lead" style="margin-bottom: 0px;">Access Token: </p>
                        <input id="accessToken" size=70 type="text" readonly style="cursor: text;" value="<?php echo $accessToken; ?>" />
                        <button id="copy-mAccessT" style="cursor: pointer" class="btn btn-copy btn-primary"><em class="icon-file"></em> Copy</button>
                    </div>
                    <div>
                        <p style="margin-bottom: 0px;">To get the user info use the following curl command: </p>
                        <input id="curlUserInfo" size=70 type="text" readonly style="cursor: text;" value="<?php echo getCurlUserInfo($accessToken, $userInfoEndpoint); ?>" />
                        <button id="copyCurlUserInfo" style="cursor: pointer" class="btn btn-copy btn-primary"><em class="icon-file"></em> Copy</button>
                    </div>
                    <?php if ($allowIntrospection) : ?>
                    <div>
                        <p style="margin-bottom: 0px;">To introspect the token use the following curl command: </p>
                        <input id="curlIntrospection" size=70 type="text" readonly style="cursor: text;" value="<?php echo getCurlIntrospect($accessToken, $introspectionEndpoint, $clientId, $clientSecret); ?>" />
                        <button id="copyCurlIntrospection" style="cursor: pointer" class="btn btn-copy btn-primary"><em class="icon-file"></em> Copy</button>
                    </div>
                    <?php endif; ?>
                    <p><?php echo $accessTokenNote; ?></p>
                    <?php if (!empty($refreshToken)) { ?>
                        <div id="refreshTokenBlock">
                            <p class="lead" style="margin-bottom: 0px;">Refresh Token: </p>
                            <input id="refreshToken" size=70 type="text" readonly style="cursor: text;" value="<?php echo $refreshToken; ?>" />
                            <button id="copyRefreshToken" style="cursor: pointer" class="btn btn-copy btn-primary"><em class="icon-file"></em> Copy</button>
                            <p><?php echo $refreshTokenNote; ?></p>
                        </div>
                        <div>
                            <p style="margin-bottom: 0px;">To generate access tokens from this refresh token use the following curl command: </p>
                            <input id="curlRefresh" size=70 type="text" readonly style="cursor: text;" value="<?php echo getCurlRefresh($refreshToken, $tokenEndpoint, $clientId, $clientSecret, $scopes); ?>" />
                            <button id="copyCurlRefresh" style="cursor: pointer" class="btn btn-copy btn-primary"><em class="icon-file"></em> Copy</button>
                            <p><?php echo $accessTokenNote; ?></p>
                        </div>
                        <br>
                    <?php } else { ?>
                        <form id="createRefreshToken" action="refreshtoken.php" method="POST">
                            <input type="hidden" name="action" value="create-refresh-token" />
                            <input class="btn btn-primary" type="submit" value="Create Refresh Token" />
                        </form>
                    <?php } ?>
                    <p><?php echo $manageTokenNote; ?><a target="_blank" class="navbar-brand" href="<?php echo $manageTokens; ?>"><?php echo $manageTokens; ?></a></p>
                </div>
                <?php if ($enableActiveTokensTable) : ?>
                <div id="refreshTokenMenu" class="tab-pane fade">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Value</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo getRefreshTokenTable($clientId, $accessToken, $issuer); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
    <script src="vendor/components/jquery/jquery.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>
    <script>
        $("#accessTokenMenu").find('.btn-copy').click(function() {
            $(this).closest('div').find('input').select();
            document.execCommand('copy');
        });
        $("#refreshTokenMenu").find('.btn-copy').click(function() {
            $(this).closest('tr').find('.token-full').select();
            document.execCommand('copy');
        });
    </script>
</body>

</html>
