<?php
  $this->set('subnavcontent', $this->element('personsubnav', array ('user_id', $user_id)));
?>

<h2> User Workshop Attendance Record </h2>

<div>
<h3> Credit Summary </h3>
<table border="0">
  <tr>
    <th>Title</th>
    <th>#Road Scholar Credits</th>
    <th>#CEU Credits</th>
    <th>#Workshops Registered</th>
    <th>#Attended</th>
  </tr>
<?php echo $html->tableCells (array (array ($user_title, 
                                            $n_credits['CreditTotal']['road_scholar_credits'],
                                            $n_credits['CreditTotal']['ceu_credits'],
                                            $n_ws_registered,
                                            $n_attended))); ?>
</table>
</div>
<br/><br/><br/>


<div>
<h3> Attendance Details </h3>
<table>
  <tr>
    <th>Name</th>
    <th>#Credits</th>
    <th>#CEU Credits</th>
    <th>Location</th>
    <th>City</th>
    <th>Held On</th>
    <th>Attended</th>
    <th>Paid</th>
    <th>Actions</th>
  </tr>
  <?php $i = 0; ?>
  <?php foreach ($user_workshops as $ws): ?>
  <tr <?php if ($i % 2) echo " class='altrow'"; ?>>
    <td> <?php echo $ws['Workshop']['Detail']['name']; ?> </td>
    <td> <?php echo $ws['Workshop']['Detail']['credits']; ?> </td>
    <td> <?php echo $ws['Workshop']['Detail']['ceu_credits']; ?> </td>
    <td> <?php echo $ws['Workshop']['location']; ?> </td>
    <td> <?php echo $ws['Workshop']['city']; ?> </td>
    <td> <?php echo $timeFormatter->commonDate ($ws['Workshop']['date']); ?> </td>
    <td> <?php if ($ws['Attendee']['attendance'] == 1) echo 'Yes'; else echo 'No'; ?> </td>
    <td> <?php echo '$' . $ws['Workshop']['total_payments']; ?> </td>
    <td>
      <div class="actions">
      <?php
        echo $html->link($html->image ('attendee-management/print-certificate.png',
                                       array ('title' => 'Print Workshop Certificate',
                                              'alt'   => 'Scroll')),
                         array ('controller' => 'workshops',
                                'action'     => 'print_certificates',
                                 $ws['Workshop']['id'],
                                 $this->data['User']['id']),
                                 array('escape' => false));
       
       echo $html->link($html->image('attendee-management/edit-payment-records.png',
                        array('title' => 'Edit Payment Records',
                              'alt'   => 'Dollar')),
                        array('controller' => 'admin',
                              'action' => 'payments',
                              $ws['Workshop']['id'],
                              $ws['Attendee']['id']),
                              array('escape' => false));
       
       echo $html->link($html->image('attendee-management/generate-invoice.png',
                        array('title' => 'Generate Invoice',
                              'alt'   => 'Dollar')),
                        array('controller' => 'attendees',
                              'action'     => 'invoice',
                              $ws['Attendee']['id']),
                        array('escape' => false));
        
       echo $html->link($html->image('attendee-management/email-invoice.png',
                        array ('title' => 'Email Invoice',
                               'alt'   => 'Envelope')),
                        array('controller' => 'attendees', 
                              'action'     => 'email_invoice',
                              $ws['Attendee']['id']),
                        array('escape' => false), 'Really send email invoice? This can\'t be undone.');
      
       echo $html->link($html->image('attendee-management/unenroll.png',
                        array('title' => 'Unenroll', 'alt' => 'X')),
                        array('controller' => 'attendees',
                              'action' => 'delete',
                              $ws['Attendee']['id']),
                        array('class' => 'unenroll',
                              'escape' => false),
                        sprintf('Really unenroll %s %s from this workshop?', $this->data['User']['first_name'], $this->data['User']['last_name']));
        ?>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
<br/><br/><br/>
