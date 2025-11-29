<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?php echo e($organization->name ?? 'Tracklet'); ?></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #7367F0; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="margin: 0;">Welcome to <?php echo e($organization->name ?? 'Tracklet'); ?>!</h1>
    </div>
    
    <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px;">
        <p>Hello <?php echo e($user->name); ?>,</p>
        
        <p>You have been added to <strong><?php echo e($organization->name); ?></strong> on Tracklet.</p>
        
        <p>Your account has been created with the following credentials:</p>
        
        <div style="background-color: white; padding: 20px; border-left: 4px solid #7367F0; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Email:</strong> <?php echo e($user->email); ?></p>
            <p style="margin: 5px 0;"><strong>Temporary Password:</strong> <code style="background-color: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-size: 16px;"><?php echo e($password); ?></code></p>
        </div>
        
        <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; color: #856404;"><strong>⚠️ Important:</strong> For security reasons, you must change your password on first login.</p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e($loginUrl); ?>" style="background-color: #7367F0; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Login to Your Account</a>
        </div>
        
        <p>After logging in, you will be prompted to change your password.</p>
        
        <p>If you have any questions, please contact your organization administrator.</p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="color: #666; font-size: 12px; margin: 0;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>



<?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/emails/user-invitation.blade.php ENDPATH**/ ?>