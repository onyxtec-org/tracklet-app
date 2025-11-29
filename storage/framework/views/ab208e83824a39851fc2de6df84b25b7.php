<?php $__env->startSection('title', 'Terms and Conditions'); ?>

<?php $__env->startSection('page-style'); ?>

<style>
  .legal-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 2rem;
    width: 100%;
  }
  .legal-content h1 {
    color: #5e5873;
    margin-bottom: 1.5rem;
    font-size: 2rem;
  }
  .legal-content h2 {
    color: #5e5873;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }
  .legal-content h3 {
    color: #5e5873;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
  }
  .legal-content p {
    margin-bottom: 1rem;
    line-height: 1.6;
    color: #6e6b7b;
  }
  .legal-content ul, .legal-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
    color: #6e6b7b;
  }
  .legal-content li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
  }
  .legal-content .last-updated {
    color: #b4b7bd;
    font-size: 0.875rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #ebe9f1;
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="legal-content">
          <a href="javascript:void(0);" class="brand-logo" style="display: flex; justify-content: center; align-items: center; margin-bottom: 2rem; padding: 0.5rem 0;">
            <img src="<?php echo e(asset('images/logo/LOGO.svg')); ?>" alt="TrackLet" style="max-width: 200px; max-height: 100px; height: auto; width: auto; object-fit: contain; display: block;">
          </a>

          <h1>Terms and Conditions</h1>
          
          <p><strong>Last Updated:</strong> <?php echo e(date('F Y')); ?></p>

          <p>Welcome to TrackLet. These Terms and Conditions ("Terms") govern your use of the TrackLet multi-organization management system ("Service") operated by TrackLet ("we," "us," or "our"). By accessing or using our Service, you agree to be bound by these Terms.</p>

          <h2>1. Acceptance of Terms</h2>
          <p>By registering for an account, accessing, or using TrackLet, you acknowledge that you have read, understood, and agree to be bound by these Terms and our Privacy Policy. If you do not agree to these Terms, you may not use the Service.</p>

          <h2>2. Description of Service</h2>
          <p>TrackLet is a comprehensive multi-organization management system that provides:</p>
          <ul>
            <li>Expense tracking and management</li>
            <li>Inventory management for consumable items and office supplies</li>
            <li>Asset management for fixed assets (laptops, furniture, equipment)</li>
            <li>Maintenance scheduling and tracking</li>
            <li>User management with role-based access control</li>
            <li>Reporting and analytics features</li>
            <li>Mobile API access</li>
          </ul>

          <h2>3. Account Registration and Organization</h2>
          <h3>3.1 Organization Accounts</h3>
          <p>Organizations can register either by:</p>
          <ul>
            <li>Self-registration through our registration form</li>
            <li>Accepting an invitation from a Super Admin</li>
          </ul>

          <h3>3.2 User Accounts</h3>
          <p>Users are created by organization administrators. Each user must:</p>
          <ul>
            <li>Use a unique email address</li>
            <li>Create a secure password (minimum 8 characters)</li>
            <li>Change their password upon first login if required</li>
            <li>Maintain the confidentiality of their account credentials</li>
          </ul>

          <h3>3.3 Account Responsibility</h3>
          <p>You are responsible for:</p>
          <ul>
            <li>Maintaining the confidentiality of your account credentials</li>
            <li>All activities that occur under your account</li>
            <li>Notifying us immediately of any unauthorized use</li>
            <li>Ensuring your organization's subscription remains active</li>
          </ul>

          <h2>4. Subscription and Payment</h2>
          <h3>4.1 Subscription Required</h3>
          <p>Organizations must maintain an active subscription to access the Service. Access to features is restricted until a subscription is activated.</p>

          <h3>4.2 Free Trial</h3>
          <p>New organizations receive a 1-month (30 days) free trial period. During the trial:</p>
          <ul>
            <li>Full access to all features is provided</li>
            <li>No charges are applied</li>
            <li>Payment method must be provided but will not be charged during trial</li>
          </ul>

          <h3>4.3 Payment Terms</h3>
          <p>After the trial period:</p>
          <ul>
            <li>Subscriptions are billed annually</li>
            <li>Payment is processed through Stripe</li>
            <li>Subscriptions automatically renew unless cancelled</li>
            <li>All fees are non-refundable except as required by law</li>
          </ul>

          <h3>4.4 Subscription Cancellation</h3>
          <p>Organizations may cancel their subscription at any time. Upon cancellation:</p>
          <ul>
            <li>Access to the Service will continue until the end of the current billing period</li>
            <li>No refunds will be provided for the remaining period</li>
            <li>Data will be retained according to our data retention policy</li>
          </ul>

          <h2>5. User Roles and Permissions</h2>
          <p>TrackLet provides role-based access control with the following roles:</p>
          <ul>
            <li><strong>Super Admin:</strong> Full access across all organizations (system administrator)</li>
            <li><strong>Admin:</strong> Full access within their organization</li>
            <li><strong>Finance:</strong> Access to Expense Tracking module</li>
            <li><strong>Admin Support:</strong> Access to Inventory, Assets, and Maintenance modules</li>
            <li><strong>General Staff:</strong> Read-only access to relevant views</li>
          </ul>
          <p>Organizations are responsible for assigning appropriate roles to their users.</p>

          <h2>6. Data and Content</h2>
          <h3>6.1 Data Ownership</h3>
          <p>Organizations retain full ownership of all data they input into the Service. TrackLet does not claim ownership of your data.</p>

          <h3>6.2 Data Isolation</h3>
          <p>Each organization's data is completely isolated. Organizations cannot access data belonging to other organizations.</p>

          <h3>6.3 Data Accuracy</h3>
          <p>You are responsible for the accuracy, completeness, and legality of all data you enter into the Service.</p>

          <h3>6.4 Data Backup</h3>
          <p>While we implement reasonable backup procedures, you are responsible for maintaining your own backups of critical data.</p>

          <h2>7. Acceptable Use</h2>
          <p>You agree not to:</p>
          <ul>
            <li>Use the Service for any illegal purpose or in violation of any laws</li>
            <li>Upload malicious code, viruses, or harmful content</li>
            <li>Attempt to gain unauthorized access to the Service or other organizations' data</li>
            <li>Interfere with or disrupt the Service or servers</li>
            <li>Use automated systems to access the Service without permission</li>
            <li>Share your account credentials with unauthorized parties</li>
            <li>Reverse engineer, decompile, or disassemble any part of the Service</li>
          </ul>

          <h2>8. Intellectual Property</h2>
          <p>The Service, including its original content, features, and functionality, is owned by TrackLet and is protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
          <p>You may not copy, modify, distribute, sell, or lease any part of the Service without our express written permission.</p>

          <h2>9. Service Availability</h2>
          <p>We strive to maintain high availability of the Service but do not guarantee:</p>
          <ul>
            <li>Uninterrupted or error-free operation</li>
            <li>That the Service will be available at all times</li>
            <li>That defects will be corrected immediately</li>
          </ul>
          <p>We reserve the right to:</p>
          <ul>
            <li>Modify or discontinue the Service with reasonable notice</li>
            <li>Perform scheduled maintenance</li>
            <li>Suspend access for violations of these Terms</li>
          </ul>

          <h2>10. Limitation of Liability</h2>
          <p>To the maximum extent permitted by law:</p>
          <ul>
            <li>TrackLet shall not be liable for any indirect, incidental, special, consequential, or punitive damages</li>
            <li>Our total liability shall not exceed the amount paid by you in the 12 months preceding the claim</li>
            <li>We are not responsible for any loss of data, profits, or business opportunities</li>
          </ul>

          <h2>11. Indemnification</h2>
          <p>You agree to indemnify and hold TrackLet harmless from any claims, damages, losses, liabilities, and expenses (including legal fees) arising from:</p>
          <ul>
            <li>Your use of the Service</li>
            <li>Your violation of these Terms</li>
            <li>Your violation of any third-party rights</li>
            <li>Any content or data you submit to the Service</li>
          </ul>

          <h2>12. Termination</h2>
          <h3>12.1 Termination by You</h3>
          <p>You may terminate your account at any time by cancelling your subscription or contacting us.</p>

          <h3>12.2 Termination by Us</h3>
          <p>We may terminate or suspend your account immediately if:</p>
          <ul>
            <li>You violate these Terms</li>
            <li>Your subscription payment fails</li>
            <li>You engage in fraudulent or illegal activity</li>
            <li>Required by law or court order</li>
          </ul>

          <h3>12.3 Effect of Termination</h3>
          <p>Upon termination:</p>
          <ul>
            <li>Your right to use the Service immediately ceases</li>
            <li>We may delete your account and data after a reasonable retention period</li>
            <li>Provisions that by their nature should survive will remain in effect</li>
          </ul>

          <h2>13. Changes to Terms</h2>
          <p>We reserve the right to modify these Terms at any time. We will:</p>
          <ul>
            <li>Notify users of material changes via email or through the Service</li>
            <li>Update the "Last Updated" date at the top of this page</li>
            <li>Provide at least 30 days' notice for significant changes</li>
          </ul>
          <p>Your continued use of the Service after changes become effective constitutes acceptance of the modified Terms.</p>

          <h2>14. Governing Law</h2>
          <p>These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which TrackLet operates, without regard to its conflict of law provisions.</p>

          <h2>15. Dispute Resolution</h2>
          <p>Any disputes arising from these Terms or the Service shall be resolved through:</p>
          <ul>
            <li>Good faith negotiation between the parties</li>
            <li>If negotiation fails, through binding arbitration in accordance with applicable arbitration rules</li>
            <li>Or through the courts of competent jurisdiction if arbitration is not available</li>
          </ul>

          <h2>16. Contact Information</h2>
          <p>If you have any questions about these Terms, please contact us at:</p>
          <ul>
            <li>Email: support@tracklet.com</li>
            <li>Through the Service's support features</li>
          </ul>

          <h2>17. Severability</h2>
          <p>If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary, and the remaining provisions shall remain in full force and effect.</p>

          <h2>18. Entire Agreement</h2>
          <p>These Terms, together with our Privacy Policy, constitute the entire agreement between you and TrackLet regarding the use of the Service and supersede all prior agreements and understandings.</p>

          <div class="text-center mt-4">
            <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">Back to Login</a>
          </div>

          <div class="last-updated">
            <p><strong>Last Updated:</strong> <?php echo e(date('F j, Y')); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/contentLayoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /opt/lampp/htdocs/tracklet-app/resources/views/legal/terms.blade.php ENDPATH**/ ?>